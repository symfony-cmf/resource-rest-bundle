Feature: Resource enhancement
    In order to add additional data to resource REST responses
    As a developer
    I need to be able to register enhancers which do this

    Background:
        Given the test application has the following configuration:
            """
            cmf_resource:
                repository:
                    doctrine_phpcr_odm:
                        phpcr_repo:
                            basepath: /tests/cmf/articles

            cmf_resource_rest:
                enhancer_map:
                    -
                        repository: phpcr_repo
                        enhancer: payload
            """


    Scenario: Retrieve PHPCR resource with children
        Given there exists a "Article" document at "/cmf/articles/foo":
            | title | Article 1 |
            | body | This is my article |
        Then I send a GET request to "/api/phpcr_repo/foo"
        And print response
        And the response should contain json:
            """
            {
                "repository_alias": "phpcr_repo",
                "repository_type": "doctrine_phpcr",
                "payload_alias": null,
                "payload_type": "nt:unstructured",
                "path": "\/foo",
                "repository_path": "\/foo",
                "children": [],
                "payload": {
                    "jcr:primaryType": "nt:unstructured",
                    "jcr:mixinTypes": [
                        "phpcr:managed"
                    ],
                    "phpcr:class": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article",
                    "phpcr:classparents": [],
                    "title": "Article 1",
                    "body": "This is my article"
                }
            }
            """
