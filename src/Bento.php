<?php

namespace Bento;


class Bento
{
    public $client;
    public $email;


    public function __construct($site_uuid = null)
    {
        $this->client = new BentoClient($site_uuid);
    }

    public function identify($email)
    {
        $this->setEmail($email);
    }

    public function tag($tag, $email = null)
    {
        //Note: check Bento docs on Tag Groups if you want to set this up before starting to tag

        $event_base = [
            'site' => $this->client->site_uuid,
            'email' => $email,
            'type' => 'import',
            'tag' => $tag
        ];

        return $this->send($event_base);
    }

    public function track($event_name, $fields = [], $email = null)
    {

        $event_base = [
            'site' => $this->client->site_uuid,
            'email' => $email,
            'type' => $event_name
        ];
        $event = array_merge($event_base, $fields);

        return $this->send($event);
    }

    public function updateFields($email = null, $fields = [])
    {
        $event_base = [
            'site' => $this->client->site_uuid,
            'email' => $email
        ];
        $event = array_merge($event_base, $fields);

        return $this->send($event);
    }

    public function send($event)
    {

        if (!isset($event['email']) || !$event['email']) {
            $event['email'] = $this->email;
            if (!$event['email']) {
                throw new \Exception('Missing email');
            }
        }

        return $this->client->send($event);
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
