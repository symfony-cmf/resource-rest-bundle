Feature: Request Resources from the REST API
    In order to retrieve data from the resource webservice
    As a webservice user
    I need to be able to query the webservice

    Background:
        Given the test application has the following configuration:
        """
        cmf_resource:
            repository:
                doctrine_phpcr_odm:
                    testrepo:
                        basepath: /tests/cmf/articles
        """


    Scenario: Retrieve a resource
        Given there exists a "Article" document at "/cmf/articles/foo":
            | title | Article 1 |
            | body | This is my article |
        Then I send a GET request to "/api/testrepo/foo"
        Then print response
        And the response code should be 200
        And the response should contain json:
        """
        {
            "name": "foo"
            "path": "/foo"
            "type": "PhpcrOdmResource
        }
        """

