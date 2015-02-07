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
                        phpcrodm_repo:
                            basepath: /tests/cmf/articles

            cmf_resource_rest:
                payload_alias_map:
                    article: 
                        repository: doctrine_phpcr_odm
                        type: "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article"
            """


    Scenario: Retrieve a PHPCR-ODM resource with children
        Given there exists a "Article" document at "/cmf/articles/foo":
            | title | Article 1 |
            | body | This is my article |
        Then I send a GET request to "/api/phpcrodm_repo/foo"
        And the response code should be 200
        And the response should contain json:
            """
            {
                "path": "\/foo",
                "repo_path": "\/foo",
                "children": [],
                "document": {
                    "id": "\/tests\/cmf\/articles\/foo",
                    "title": "Article 1",
                    "body": "This is my article",
                    "_links": {
                        "self": {
                            "href": "\/path\/to\/this"
                        }
                    }
                },
                "repository_alias": "phpcrodm_repo",
                "repository_type": "doctrine_phpcr_odm",
                "payload_alias": "article",
                "_links": {
                    "self": {
                        "href": "\/api\/phpcrodm_repo\/foo"
                    }
                    }
            }
            """
