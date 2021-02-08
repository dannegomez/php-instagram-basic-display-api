<?php

/**
 * Simpel wrapper class for getting posts from Instagram Basic Display API
 *
 * @author     Daniel Gomez-Ortega
 * @link       https://github.com/dannegomez
 * 
 * For more information also see
 * @link       https://developers.facebook.com/docs/instagram-basic-display-api
 */

class Instagram
{
    /**
     * Access token of user
     *
     * Generated access token for instagram api
     * https://developers.facebook.com/docs/instagram-basic-display-api/overview#instagram-user-access-tokens
     *
     * @var string
     */
    protected $access_token;

    /**
     * Endpoints to Instagrams api
     *
     * @var array
     */
    protected $endpoints = [
        'user' => 'https://graph.instagram.com'
    ];

    /**
     * Pagination next url
     *
     * @var string
     */
    protected $pagination_next;

    /**
     * Constructor
     *
     * @param string $access_token
     */
    function __construct($access_token)
    {
        $this->access_token = $access_token;
    }


    /**
     * Get a User?s Profile
     * 
     * @param string $user_id Id of user or 'me'
     * @param string $fields Fields from user account: id,username
     *
     * @return array $response Array with result
     */
    public function getUserData($user_id = "me", $fields = "id,username")
    {
        /*
        Query the User Node
        GET /me?fields={fields}&access_token={access-token}
        */

        $url = $this->endpoints['user'] . "/{$user_id}?fields=" . trim($fields) . "&access_token=" . $this->access_token;

        $response = $this->curl($url, [], true);

        return $response;
    }


    /**
     * Get a User?s Media
     * 
     * @param string $user_id Id of user or 'me'
     * @param string $fields Fields from user media: id,media_type,media_url,permalink
     *
     * @return array $response Array with result
     */
    public function getUserMedia($user_id = "me", $fields = "id,media_type,media_url,permalink")
    {
        /*
        Query the User Media Edge
        GET /me/media?fields={fields}&access_token={access-token}
        */

        $url = $this->endpoints['user'] . "/{$user_id}/media?fields=" . trim($fields) . "&access_token=" . $this->access_token;

        $response = $this->curl($url, [], true);

        if (array_key_exists('paging', $response) && array_key_exists('next', $response['paging'])) {
            $this->pagination_next = $response['paging']['next'];
        }

        return $response;
    }


    /**
     * Get a User?s Media Pagination next
     * 
     * @return array $response Array with result
     */
    public function getUserMediaNext()
    {
        $url = $this->pagination_next;

        $response = $this->curl($url, [], true);

        return $response;
    }


    /**
     * Get Album Contents
     * 
     * @param string $media_id Id of media
     * @param string $fields Fields from user media: id,media_type,media_url,permalink
     *
     * @return array $response Array with result
     */
    public function getUserMediaChildren($media_id = "", $fields = "id,media_type,media_url,permalink")
    {
        /*
        Query the Media Children Edge
        GET /{media-id}/children?fields={fields}&access_token={access-token}
        */

        $url = $this->endpoints['user'] . "/{$media_id}/children?fields=" . trim($fields) . "&access_token=" . $this->access_token;

        $response = $this->curl($url, [], true);

        return $response;
    }


    /**
     * Php curl funtion
     * 
     * @param string $url Url to get/post data from/to
     * @param array $data Data to post
     * @param array $decode Return result as php array
     * @return mixed
     */
    public function curl($url, $data = [], $decode = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        if (isset($_SERVER["HTTP_USER_AGENT"])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        }

        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        if ($decode) {
            return json_decode($result, true);
        } else {
            return $result;
        }
    }
}
