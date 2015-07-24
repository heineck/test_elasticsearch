<?php

// elasticsearch --config=/usr/local/opt/elasticsearch/config/elasticsearch.yml UPANDO O ELASTIC_SEARCH

require 'vendor/autoload.php';

$params = array();
//$params['hosts'] = array (
//    '192.168.1.1:9200',                 // IP + Port
//    '192.168.1.2',                      // Just IP
//    'mydomain.server.com:9201',         // Domain + Port
//    'mydomain2.server.com',             // Just Domain
//    'https://localhost',                // SSL to localhost
//    'https://192.168.1.3:9200',         // SSL to IP + Port
//    'http://user:pass@localhost:9200',  // HTTP Basic Auth
//    'https://user:pass@localhost:9200',  // SSL + HTTP Basic Auth
//);
$params['hosts'] = array (
    'localhost:9200',                // SSL to localhost
);

$client = new Elasticsearch\Client($params);


$t = $_GET['t'];

try {
    switch (strtoupper($t)) {
        case 'GET':

            $params = array();
            $params['index'] = 'acotel';
            $params['type']  = 'eiplus_dev_status';
            $params['id']    = '5521983059028';
            
            if ($client->exists($params)) {
            
                $retDoc = $client->get($params);

                print_r($retDoc['_source']);
                
            } else {
                
                echo "INEXISTENT VALUE";
                
            }
            
            

            break;
        case 'SEARCH':

            $searchParams['index'] = 'acotel';
            $searchParams['type']  = 'eiplus_dev_status';
            $searchParams['body']['query']['match']['msisdn'] = '5521983059028';
            $queryResponse = $client->search($searchParams);

            print_r($queryResponse);
            
            //echo $queryResponse['hits']['hits'][0]['_id']; // Outputs 'abc'

            break;
        case 'SAVE':

            $params = array();
            $params['body']  = array('msisdn' => '5521983059028','is_free'=>'1','free_until'=>'');
            $params['index'] = 'acotel';
            $params['type']  = 'eiplus_dev_status';
            $params['id']    = '5521983059028';
            
            $ret = $client->index($params);
            
            if ($ret['created']) {
                
                echo "VALUE CREATED<BR/>\n";
                
            } else {
                $params['body']['doc']  = array('msisdn' => '5521983059028','is_free'=>'1','free_until'=>'2015-07-25');
                
                $ret = $client->update($params);
                
                echo "VALUE UPDATED<BR/>\n";
            }
            
            
            break;
        case 'DELETE_DOC';

            $params = array();
            $params['index'] = 'acotel';
            $params['type']  = 'eiplus_dev_status';
            $params['id']    = '5521983059028';
            
            if ($client->exists($params)) {
            
                echo "DELETING VALUE...<BR/>\n";
                
                $retDelete = $client->delete($params);
            
            } else {
                echo "INEXISTENT VALUE...<BR/>\n";
            }

            break;
        case 'DELETE_IDX':
            
            $deleteParams['index'] = 'acotel';
            $client->indices()->delete($deleteParams);
            
            break;
        default:
            die('INVALID PARAMETER: Type GET,SAVE OR DELETE');
    };
} catch (Exception $e) {
    switch ($e->getCode()) {
        case 0:

            echo "========ELASTIC_SEARCH DOWN=========";

            break;

        default:
            
            echo "EXCEPTION LAUNCHED: [{$e->getCode()}] - {$e->getMessage()}";
            
            break;
    }
    
}


?>