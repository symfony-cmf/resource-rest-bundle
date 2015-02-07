Feature: Request Resources from the REST API
    In order to retrieve data from the resource webservice
    As a webservice user
    I need to be able to query the webservice

    Background:
        Given the test application has the following configuration:
            """
            cmf_resource:
                repository:
                    doctrine_phpcr:
                        phpcr_repo:
                            basepath: /tests/cmf/articles


            cmf_resource_rest:
                payload_alias_map:
                    article:
                        repository: doctrine_phpcr
                        type: "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article"
            """


    Scenario: Retrieve PHPCR resource with children
        Given there exists a "Article" document at "/cmf/articles/foo":
            | title | Article 1 |
            | body | This is my article |
        Then I send a GET request to "/api/phpcr_repo/foo"
        And the response should contain json:
            """
            {
                "path": "\/foo",
                "repo_path": "\/foo",
                "children": [],
                "node": {
                    "jcr:primaryType": "nt:unstructured",
                    "jcr:mixinTypes": [
                        "phpcr:managed"
                    ],
                    "phpcr:class": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article",
                    "phpcr:classparents": [],
                    "title": "Article 1",
                    "body": "This is my article"
                },
                "repository_alias": "phpcr_repo",
                "repository_type": "doctrine_phpcr",
                "payload_alias": "nt:unstructured",
                "_links": {
                    "self": {
                        "href": "\/api\/phpcr_repo\/foo"
                    }
                }
            }
            """
