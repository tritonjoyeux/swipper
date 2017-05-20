## Search with session
The search with Session use [Mink](http://mink.behat.org/en/latest/index.html)

You can use `mink` through the `session` attribute defined in the `constructor`

Mink (`session`) reacted like a browser. 

You can manipulate and retrieve any kind of dom element and interact with them.

## Usage

<h3>. <u>Browser</u></h3>
* You need to set the `URL` you want to parse with the method `visit()`
* You can access to the browser history : 

    ```
    $session->reload();
    $session->back();
    $session->forward();
    ```

* You can manipulate cookies : 

    ```
    // set cookie:
    $session->setCookie('cookie name', 'value');
    // get cookie:
    echo $session->getCookie('cookie name');
    // delete cookie:
    $session->setCookie('cookie name', null);
    ```

* You can retrieve any information : 

    ```
    echo $session->getStatusCode();
    ```

* You can set a basic authentication : 

    ```
    $session->setBasicAuth($user, $password);
    $session->setBasicAuth(false);
    ```     

<h3>. <u>Pages</u></h3>

* You need to retrieve the `DocumentElement` using the `getPage()` method

    ```
    $page = $session->getPage();
    ```

* Now you can manipulate this `DocumentElement` to get `NodeElement`

    ```
    $nodeElements = $page->findAll('css', '.logo');
    // return an array of all NodeElement match or null if there is no result 
    $nodeElement = $page->find('css', '.logo');
    // return the first NodeElement match or null if there is no result
    $nodeElement = $page->findById('logo');
    // looks for a child element with the given id
    $nodeElement = $page->findLink('logo');
    // looks for a link with the given text, title, id or alt attribute
    $nodeElement = $page->findButton('logo');
    // looks for a button with the given text, title, id, name attribute or alt attribute
    $nodeElement = $page->findField('logo');
    // looks for a field (input, textarea or select) with the given label, placeholder, id or name attribute
    ```

<h3>. <u>NodeElement</u></h3>

* Once you have get your NodeElement(s)

    * Now you can get informations of this NodeElement 
    
        - HTML attributes
        
        ```
        $element = $page->find('css', '.something');
        
        // get tag name:
        echo $element->getTagName(); // displays 'a'
        
        if ($element->hasAttribute('href')) {
            echo $element->getAttribute('href');
        } else {
            echo 'This anchor is not a link. It does not have an href.';
        }
        
        $element->hasClass();
        ```

        - Element Content and Text
        
        ```
        $element = $page->find('css', '.something');
        
        $element->getHtml();
        //gets the inner HTML of the element, i.e. all children of the element.
        
        $element->getText();
        //gets the outer HTML of the element, i.e. including the element itself.

        $element->getOuterHtml();
        //gets the text of the element.
        //
        ```
    * You can interact with this NodeElement
    
        - Interact with the mouse
        
        ```
        $element = $page->find('css', 'button');
        $element->click();
        $element->doubleClick();
        $element->rightClick();
        $element->mouseOver();
        ```
## Example

Example of Agent using session [here](../src/Agents/Cos/CosAgent.php)

