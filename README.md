<h1>Swiper</h1>
![swiper](https://vignette3.wikia.nocookie.net/doratheexplorer/images/6/63/Swiper.png/revision/latest?cb=20131219004140)
<b>Swiper</b> is based on symfony. 
It is inspired by dependences injection of symfony.

## Install
<h4><b>1.</b> Clone</h4>
<b>ssh</b> : `git clone git@gitlab.fashiongroup.com:fashiongroup/swiper.git`<br>
<b>http</b> : `git clone http://gitlab.fashiongroup.com:8081/fashiongroup/swiper.git`

<h4><b>2.</b> Install packages</h4>
Install via composer : `composer install`

<h4><b>3.</b> Update parameters.yml</h4>
In the directory `config` you will find [parameters.yml.dist](config/parameters.yml.dist)
* Create a new file `parameters.yml`.
* Copy the content of dist in the new.
* Set your uri api.

<br>

_<b>Swiper is ready to use</b>_

## Usage
<h4><b>1.</b> Set agents</h4>
* In the directory `src/Di/config/` you will find the [agents.yml](src/Di/config/agents.yml) file and the `agents/` directory.
* Create a yml file in the `agents/` directory with the name of your agent.
* Set the class and the parent of your agent. (example [session](src/Di/config/agents/cos.yml), [rss](src/Di/config/agents/randa.yml), [api](src/Di/config/agents/career_next.yml)).
* Create the directory agent in the `src/Agents/` folder and name it : your agent with the first character in uppercase.
* Create the class in this folder with this syntax : `SamenameoffolderAgent.php`.
    * Add the appropriate namespace.
    * Extends the [AbstractAgent](src/Agents/AbstractAgent.php).
* The class must implement the [AgentInterface](src/Agents/AgentInterface.php).
    * Set the `__constructor()` : (depends of his parent : example [session](src/Agents/Cos/CosAgent.php), [rss](src/Agents/Randa/RandaAgent.php), [api](src/Agents/CareerNext/CareerNextAgent.php)).
    * Set the `getName()` : it must return the name of the agent in lowercase.
    * Set the `refine()` : it must have a parameter ([JobPosting](src/Model/JobPosting.php)) and return a JobPosting.
    * Set the `search()` : it must return a [JobPostingResultSet](src/Agents/JobPostingResultSet.php) with 2 parameters : an ArrayCollection and an array for the cursor (if the cursor == false the search will stop and the refine will be call for all JobPostings).
* Then run this command to clear the [contrainer.php](cache/) in the [cache](cache/) folder :
    ``` 
    php bin/swiper cc
    ```

<h4><b>2.</b> Run command</h4>
There are 4 possible commands : 
* This command clears the data 
    ``` 
    php bin/swiper cd
    ```

* This command clears the logs 
    ``` 
    php bin/swiper clo
    ```

* This command remove the container.php 
    ``` 
    php bin/swiper cc
    ```

* This command run <b>swiper</b>
    ``` 
    php bin/swiper run
    ```

<h4>Differents searches</h4>
<u>Single search</u>
- There is few options you can set when launching <b>swiper</b>
    - Require : 
        - `--agent` (name of the agent)
    - Optional : 
        - `--location` (location option, by default `null`)
        - `--freshness` (freshness option, by default `2`, possibilities : an `integer`)
        - `--writers` (writers option, by default `console`, possibilities : `console` and `api`)
        - `--terms` (terms option, by default `null`, possibilities : a `string`)
        - `--country` (country option, by default `null`, possibilities : a `string`)
        - `--extras` (extras option, by default `null`, possibilities : a json `string`)
        
    <b>These options can be retrieved using the [getSearch()](src/Agents/AbstractAgent.php) method</b>

<u>Multiple search</u>
- If you want to run multiple search you must : 
    - Create a json like [this](swiper/search.json)    
    - Add the absolute path of this file

<h4>Differents types</h4>
<b>[Session](doc/session.md)</a></b>, <b>[Guzzle](doc/guzzle.md)</b>, <b>[Flux](doc/flux.md)</b>

## Save on appli
You must create a json like this in `config/job_posting/sources/` folder :

    ```
    {
      "name" : "cos",
      "display": false,
      "overrides": {
        "societe": "COS Collection Of Style",
        "id_societe": 43092
      }
    }
    ```

Name is the name of the Agent.<br>
Display is to display the ad.<br>
Overrides is to set the ad model.<br>

## Commands available 
<b>[Here](doc/commands.md)</b>

## Troobleshooting
* Common error : 
UnexpectedValueException' with message 'Overloading of string functions using mbstring.func_overload is not supported by phpseclib

* Resolution : execute PHP Cli with mbstring func_overload disabled
```
php -d mbstring.func_overload=0 vendor/bin/dep
```

## Deploy
* Preprod : 
    ```
    vendor/bin/dep deploy
    ```
* Prod : 
    ```
    vendor/bin/dep deploy cron
    ```
<b>+</b>
Deploy <b>appli</b> for json files