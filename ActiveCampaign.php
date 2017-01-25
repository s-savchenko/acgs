<?php

/**
 * Created by PhpStorm.
 * User: savchenko
 * Date: 24.01.17
 * Time: 23:09
 */
class ActiveCampaign
{
    const REQUEST_TYPE_SUBSCRIBE = 'subscribe';
    const REQUEST_TYPE_UNSUBSCRIBE = 'unsubscribe';
    const REQUEST_TYPE_UPDATE = 'update';

    protected $apiKey = false;
    protected $apiUrl = false;
    protected $apiOutput = 'json';

    public function __construct($apiKey, $apiUrl)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    protected function request($action, $params, $outputFormat = 'json', $full = true)
    {
        $params = array_merge($params, [
            'api_key' => $this->apiKey,
            'api_action' => $action,
            'api_output' => $outputFormat,
            'full' => $full ? 1 : 0,
            'page' => 1
        ]);

        $query = '';
        foreach($params as $key => $val)
            $query .= sprintf('%s=%s&', urlencode($key), urlencode($val));
        $query = rtrim($query, '& ');

        $url = rtrim($this->apiUrl, '/ ');
        $url = $url . '/admin/api.php?' . $query;

        $request = curl_init($url);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);

        $response = (string)curl_exec($request);
        curl_close($request);
        return json_decode($response, true);
    }

    public function getContactList($contactIds)
    {
        $result = $this->request('contact_list', [
            'ids' => implode(',', $contactIds)
        ]);

        $contacts = [];
        if (is_array($result) && array_key_exists('result_code', $result) && $result['result_code'] == 1) {
            foreach ($result as $key => $val) {
                if (is_int($key)) {
                    $contact = $result[$key];
                    if (array_key_exists('lists', $contact) && is_array($contact['lists'])) {
                        $listNames = array_map(function ($list) {
                            return $list['listname'];
                        }, $contact['lists']);
                        $contact['list_names'] = implode(',', $listNames);
                    }
                    if (array_key_exists('tags', $contact) && is_array($contact['tags'])) {
                        $contact['tag_names'] = implode(',', $contact['tags']);
                    }
                    $contacts[] = $contact;
                }
            }
        }

        return $contacts;
    }
}