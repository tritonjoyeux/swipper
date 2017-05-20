## Search with client
The search with Client use [Guzzle](http://docs.guzzlephp.org/en/latest/)

You can use `guzzle` through the `client` attribute defined in the `constructor`

Guzzle (`client`) is used tu communicate with api. 

You can retrieve all information from an API and manipulate them.

## Usage

<h3>. <u>Settings</u></h3>
* You can send request to the API and get the response : 
    - GET
    
        ```
        $response = $client->request('GET', 'https://api.github.com/user', [
            'auth' => ['user', 'pass']
        ]);
        //or
        $response = $client->get('https://api.github.com/user', [
            'auth' => ['user', 'pass']
        ]);
        ```
    - POST
     
        ```
        $response = $client->request('POST', 'https://api.github.com/user', [
            'auth' => ['user', 'pass']
        ]);
        //or
        $response = $client->post('https://api.github.com/user', [
            'auth' => ['user', 'pass']
        ]);
        ```
    - PUT
    
        ```
        $response = $client->request('PUT', 'https://api.github.com/user', [
            'auth' => ['user', 'pass']
        ]);
        //or
        $response = $client->put('https://api.github.com/user', [
            'auth' => ['user', 'pass']
        ]);
        ```
* You can get information of the response : 

    ```
    echo $response->getStatusCode();
    // "200"
    echo $response->getHeader('content-type');
    // 'application/json; charset=utf8'
    echo $response->getBody();
    // {"type":"User"...'
    ```

* You can create custom request : 

    ```
    // Create a PSR-7 request object to send
    $headers = ['X-Foo' => 'Bar'];
    $body = 'Hello!';
    $request = new Request('HEAD', 'http://httpbin.org/head', $headers, $body);

    //and then
    $client->send($request);
    ```

* You can set a basic authentication : 

    ```
    $session->setBasicAuth($user, $password);
    $session->setBasicAuth(false);
    ```     

<h3>. <u>Informations</u></h3>

* `JSON` example :

    ```
    $json = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    ```

## Example

Example of Agent using session [here](../src/Agents/CareerNext/CareerNextAgent.php)

