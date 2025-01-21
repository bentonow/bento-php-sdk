module.exports = async ({github, context}) => {
    const defaultBranch = 'master'; // Changed from 'main' to 'master'
    
    // Get the latest tag
    let latestTag = null;
    try {
        const { data: tags } = await github.rest.repos.listTags({
            owner: context.repo.owner,
            repo: context.repo.repo,
            per_page: 1
        });
        latestTag = tags[0]?.name || null;
    } catch (error) {
        console.log('No previous tags found');
    }

    // Get commits since last tag or beginning of history if no tags
    let commits;
    try {
        const { data } = await github.rest.repos.compareCommits({
            owner: context.repo.owner,
            repo: context.repo.repo,
            base: latestTag || `${defaultBranch}~10`, // If no tags, get last 10 commits
            head: defaultBranch
        });
        commits = data;
    } catch (error) {
        // If the comparison fails (e.g., with a new repo), get the latest commits
        const { data } = await github.rest.repos.listCommits({
            owner: context.repo.owner,
            repo: context.repo.repo,
            sha: defaultBranch,
            per_page: 10
        });
        commits = {
            commits: data
        };
    }

    // Get PRs
    const { data: pulls } = await github.rest.pulls.list({
        owner: context.repo.owner,
        repo: context.repo.repo,
        state: 'closed',
        sort: 'updated',
        direction: 'desc',
        per_page: 100
    });

    // Filter merged PRs since last release
    const mergedPRs = pulls.filter(pr => {
        return pr.merged_at && (!latestTag || new Date(pr.merged_at) > new Date(latestTag.created_at));
    });

    // Rest of the existing code remains the same...
    const getChangeType = (subject, body = '') => {
        const text = `${subject}\n${body}`.toLowerCase();
        if (text.includes('breaking change') ||
            text.includes('breaking:') ||
            text.includes('bc break') ||
            text.includes('backwards compatibility')) return 'breaking';
        if (text.includes('feat:') ||
            text.includes('feature:') ||
            text.includes('enhancement:') ||
            text.includes('new class') ||
            text.includes('new interface')) return 'feature';
        if (text.includes('fix:') ||
            text.includes('bug:') ||
            text.includes('hotfix:') ||
            text.includes('patch:')) return 'bug';
        if (text.includes('dependency') ||
            text.includes('upgrade') ||
            text.includes('bump')) return 'dependency';
        if (text.includes('doc:') ||
            text.includes('docs:')) return 'docs';
        if (text.includes('test:') ||
            text.includes('test') ||
            text.includes('coverage')) return 'test';
        if (text.includes('chore:') ||
            text.includes('refactor:') ||
            text.includes('style:') ||
            text.includes('ci:') ||
            text.includes('lint')) return 'maintenance';
        return 'other';
    };

    // Categories remain the same...
    const categories = {
        '🚀 New Features': {
            commits: commits.commits.filter(commit =>
                getChangeType(commit.commit.message) === 'feature'
            ),
            prs: mergedPRs.filter(pr =>
                getChangeType(pr.title, pr.body) === 'feature'
            )
        },
        '🐛 Bug Fixes': {
            commits: commits.commits.filter(commit =>
                getChangeType(commit.commit.message) === 'bug'
            ),
            prs: mergedPRs.filter(pr =>
                getChangeType(pr.title, pr.body) === 'bug'
            )
        },
        '📦 Dependencies': {
            commits: commits.commits.filter(commit =>
                getChangeType(commit.commit.message) === 'dependency'
            ),
            prs: mergedPRs.filter(pr =>
                getChangeType(pr.title, pr.body) === 'dependency'
            )
        },
        '📚 Documentation': {
            commits: commits.commits.filter(commit =>
                getChangeType(commit.commit.message) === 'docs'
            ),
            prs: mergedPRs.filter(pr =>
                getChangeType(pr.title, pr.body) === 'docs'
            )
        },
        '🧪 Tests': {
            commits: commits.commits.filter(commit =>
                getChangeType(commit.commit.message) === 'test'
            ),
            prs: mergedPRs.filter(pr =>
                getChangeType(pr.title, pr.body) === 'test'
            )
        },
        '🔧 Maintenance': {
            commits: commits.commits.filter(commit =>
                getChangeType(commit.commit.message) === 'maintenance'
            ),
            prs: mergedPRs.filter(pr =>
                getChangeType(pr.title, pr.body) === 'maintenance'
            )
        },
        '🔄 Other Changes': {
            commits: commits.commits.filter(commit =>
                getChangeType(commit.commit.message) === 'other'
            ),
            prs: mergedPRs.filter(pr =>
                getChangeType(pr.title, pr.body) === 'other'
            )
        }
    };

    // Generate markdown
    let markdown = `## Release v${process.env.VERSION}\n\n`;
    
    // Add requirements information
    markdown += '### Requirements\n\n';
    markdown += '* The Bento PHP SDK requires PHP 7.4+\n';
    markdown += '* Composer\n';
    markdown += '* Bento API keys\n\n';

    // Add breaking changes first
    const breakingChanges = [
        ...commits.commits.filter(commit => getChangeType(commit.commit.message) === 'breaking'),
        ...mergedPRs.filter(pr => getChangeType(pr.title, pr.body) === 'breaking')
    ];
    
    if (breakingChanges.length > 0) {
        markdown += '⚠️ **Breaking Changes**\n\n';
        breakingChanges.forEach(change => {
            if ('number' in change) {
                markdown += `* ${change.title} (#${change.number})\n`;
            } else {
                const firstLine = change.commit.message.split('\n')[0];
                markdown += `* ${firstLine} (${change.sha.substring(0, 7)})\n`;
            }
        });
        markdown += '\n';
    }

    // Add categorized changes
    for (const [category, items] of Object.entries(categories)) {
        if (items.commits.length > 0 || items.prs.length > 0) {
            markdown += `### ${category}\n\n`;
            items.prs.forEach(pr => {
                markdown += `* ${pr.title} (#${pr.number}) @${pr.user.login}\n`;
            });
            items.commits
                .filter(commit => !items.prs.some(pr => pr.merge_commit_sha === commit.sha))
                .forEach(commit => {
                    const firstLine = commit.commit.message.split('\n')[0];
                    markdown += `* ${firstLine} (${commit.sha.substring(0, 7)}) @${commit.author?.login || commit.commit.author.name}\n`;
                });
            markdown += '\n';
        }
    }

    // Add installation instructions
    markdown += '### Installation\n\n';
    markdown += '```bash\n';
    markdown += 'composer require bentonow/bento-php-sdk\n';
    markdown += '```\n\n';

    // Add contributors section
    const contributors = new Set([
        ...mergedPRs.map(pr => pr.user.login),
        ...commits.commits.map(commit => commit.author?.login || commit.commit.author.name)
    ]);
    
    if (contributors.size > 0) {
        markdown += '## Contributors\n\n';
        [...contributors].forEach(contributor => {
            markdown += `* ${contributor.includes('@') ? contributor : '@' + contributor}\n`;
        });
    }

    return markdown;
}
