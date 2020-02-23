<?php

namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class GICEProvider extends AbstractProvider implements ProviderInterface
{

    protected $scopeSeparator = ' ';

     /**
     * {@inheritdoc}
     */
     protected function getAuthUrl($state)
     {
       return $this->buildAuthUrlFromBase('https://esi.goonfleet.com/oauth/authorize', $state);
     }
    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
    	return 'https://esi.goonfleet.com/oauth/token';
    }
    /**
     * {@inheritdoc}
     */
    public function getAccessToken($code)
    {
    	$response = $this->getHttpClient()->post($this->getTokenUrl(), [
    		'headers' => ['Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)],
    		'body'    => $this->getTokenFields($code),
    	]);

    	return $this->parseAccessToken($response->getBody());
    }
    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {

      return array_add(
        parent::getTokenFields($code), 'grant_type', 'authorization_code'
      );
    }
    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {

    	$response = $this->getHttpClient()->get('https://esi.goonfleet.com/oauth/userinfo', [
    		'headers' => [
    			'Authorization' => 'Bearer ' . $token,
    		],
    	]);

    	return json_decode($response->getBody(), true);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {

        #dd($user);

        /*
        array:4 [▼
          "sub" => "4444"
          "name" => "scopehone"
          "username" => "scopehone"
          "pri_grp" => "0"
        ]
        */

        return (new User)->setRaw($user)->map([
        	'sub'           => $user['sub'],
        	'name'          => $user['name'],
        	'username'      => $user['username'],
        	'pri_grp'       => $user['pri_grp']
        ]);
      }


    }
