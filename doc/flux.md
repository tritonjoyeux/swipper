## Search with flux
The search with Flux use [simplexml_load_string](http://php.net/manual/fr/function.simplexml-load-string.php). This is a function of `PHP`

It use the [RssParser.php](../src/Rss/RssParser.php)

You can retrieve all information from an `FLUX` and manipulate them.

## Usage

Based on this XML

```
<JOBS>
    <JOB>
        <JOBTITLE>
            <![CDATA[ Account Executive ]]>
        </JOBTITLE>
        <JOBLOCATION>
            <![CDATA[ New York, NY-NY ]]>
        </JOBLOCATION>
        <JOBBODY>
            <![CDATA[
            * Ability to optimize and grow key account/s * Partner internally with merchandising, marketing, packaging, account receivables and business planning to achieve all shipment and financial objectives * Partner with key personnel in retail accounts to maximize top line shipments and increase
            ]]>
            <![CDATA[
            penetration of Randa products and programs * Communicate with management internal and external on retail trends, retail results, challenges and opportunities * Responsible for managing all client/s projects execution and timelines * Coordinate details of client/s projects with sales and field team * Manage day to day service issues and communication from client/s * Assist with handling of daily call outs, monitoring of projects, and ensure accuracy, & completion * Responsible for service budget for select accounts * Works closely with sales management, field services and account management to develop and maintain best practices for excellent customer satisfaction and execution of projects * Manage top line shipments of a key merchandise category for an account structure * Determine assortment proposals utilizing financial objectives in partnership with business planning * Develop in depth product knowledge for all key merchandising categories * Identify and open business relationships with new accounts for Brand and classification growth * Responsible for the logistical planning of monthly projects for specific accounts * Responsible for achieving all financial objectives for all accounts within the structure * Must be a self-starter and be able to maximize business through building partnerships with clients developing opportunities Bachelor's Degree Advanced knowledge of Microsoft Office -- Excel, Word, Outlook; & proficient understanding & execution of "Retail Math" Strong written, verbal, and organizational skills Highly motivated, professional, & dependable, Results Driven, Ability to multi-task Ability to problem-solve, work under pressure, & manage time appropriately Ability to think "out of the box" Energetic, enthusiastic, and prone to take action Build relationships with the team, field services team, and customers. Team player - partnerships Ability to analyze and interpret information promptly Detailed oriented and excellent follow through skills Project Management experience 5+ years of experience in Retail Industry 3+ years of sales experience Prior knowledge of Merchandising Service Organizations a plus Experience in working with manufactures and/or retailers in the Mid-Tier & Department store channel Some travel will be required Equal Opportunity Employer Minorities/Women/Protected Veterans/Disabled
            ]]>
        </JOBBODY>
        <JOBLINK>
            <![CDATA[
            https://recruiting.adp.com/srccsh/public/RTI.home?r=5000174402606&c=1101341&d=ExternalCareerSite&rb=???
            ]]>
        </JOBLINK>
        <REQNUMBER>
            <![CDATA[ 5000174402606 ]]>
        </REQNUMBER>
        <CATEGORY>
            <![CDATA[ ]]>
        </CATEGORY>
        <FULLTIME>
            <![CDATA[ Full-time ]]>
        </FULLTIME>
        <LOCATIONCITY>
            <![CDATA[ New York ]]>
        </LOCATIONCITY>
        <LOCATIONSTATE>
            <![CDATA[ NY ]]>
        </LOCATIONSTATE>
        <LOCATIONZIP>
            <![CDATA[ 10016 ]]>
        </LOCATIONZIP>
        <LOCATIONCOUNTRY>
            <![CDATA[ USA ]]>
        </LOCATIONCOUNTRY>
        <OPENDATE>
            <![CDATA[ 1/3/2017 ]]>
        </OPENDATE>
        <EXPIREDATE>
            <![CDATA[ ]]>
        </EXPIREDATE>
        <COMPANY>
            <![CDATA[ Randa Accessories ]]>
        </COMPANY>
    </JOB>
    <JOB>
        <JOBTITLE>
            <![CDATA[ Account Executive - Men's Neckwear ]]>
        </JOBTITLE>
        <JOBLOCATION>
            <![CDATA[ New York, NY-NY ]]>
        </JOBLOCATION>
        <JOBBODY>
            <![CDATA[
            * Develop annual sales plan in support of organization strategies and objectives * Create a culture of success and ongoing business and goal achievement * Build, develop and manage the sales team, operations and resources to deliver profitable growth * Manage customer expectations and contribute to a high level of customer satisfaction * Define sales processes that drive desired sales outcomes and identify improvements where and when required * Partner internally with merchandising, marketing, packaging, account receivables and business planning to achieve all shipment and financial objectives * Put in place infrastructure and systems to support the success of the sales function * Provide detailed and accurate sales forecasting * Monitor customer, market and competitor activity and provide feedback to company leadership team and other company functions * Manage key customer relationships and participate in closing strategic opportunities * Bachelor's Degree * Advanced knowledge of Microsoft Office -- Excel, Word, Outlook; & proficient understanding & execution of "Retail Math" * Strong written, verbal, and organizational skills * Highly motivated, professional, & dependable, Results Driven, Ability to multi-task * Ability to problem-solve, work under pressure, & manage time appropriately * Ability to think "out of the box" * Energetic, enthusiastic, and prone to take action * Build relationships with the team, field services team, and customers. Team player - partnerships * Ability to analyze and interpret information promptly * Detailed oriented and excellent follow through skills * Project Management experience * 5+ years of experience in Retail Industry * 4+ years of sales experience * 4+ years of sales experience in wholesale sales & planning/forecasting * Experience in working with manufactures and/or retailers in the Mid-Tier & Department store channel * Department store experience required * Some travel will be required Equal Opportunity Employer Minorities/Women/Protected Veterans/Disabled
            ]]>
        </JOBBODY>
        <JOBLINK>
            <![CDATA[
            https://recruiting.adp.com/srccsh/public/RTI.home?r=5000205404706&c=1101341&d=ExternalCareerSite&rb=???
            ]]>
        </JOBLINK>
        <REQNUMBER>
            <![CDATA[ 5000205404706 ]]>
        </REQNUMBER>
        <CATEGORY>
            <![CDATA[ ]]>
        </CATEGORY>
        <FULLTIME>
            <![CDATA[ Full-time ]]>
        </FULLTIME>
        <LOCATIONCITY>
            <![CDATA[ New York ]]>
        </LOCATIONCITY>
        <LOCATIONSTATE>
            <![CDATA[ NY ]]>
        </LOCATIONSTATE>
        <LOCATIONZIP>
            <![CDATA[ 10016 ]]>
        </LOCATIONZIP>
        <LOCATIONCOUNTRY>
            <![CDATA[ USA ]]>
        </LOCATIONCOUNTRY>
        <OPENDATE>
            <![CDATA[ 4/10/2017 ]]>
        </OPENDATE>
        <EXPIREDATE>
            <![CDATA[ ]]>
        </EXPIREDATE>
        <COMPANY>
            <![CDATA[ Randa Accessories ]]>
        </COMPANY>
    </JOB>
</JOBS>
```

* First you need tu set the URL and get the content : for that u can use the [RssParser.php](../src/Rss/RssParser.php) methods

    ```
    $json = $this->rss->setUrlXml($url)->fetch();
    //this will return an array of informations
    ```

* Then you can `foreach` this result to get all informations

    ```
    foreach ($annonces->JOB as $annonce) {
        $description = $annonce->JOBBODY->__toString();
        //the method __toString() is used to get the result in a string
    }
    ```
    
## Example

Example of Agent using session [here](../src/Agents/Randa/RandaAgent.php)

