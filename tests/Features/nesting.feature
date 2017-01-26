Feature: Nesting resources
    In order to retrieve a tree of data
    As a webservice user
    I need to be able to get nested resources

    Background:
        Given the test application has the following configuration:
            """
            cmf_resource:
                repositories:
                    default:
                        type: doctrine_phpcr_odm
                        basepath: /tests/cmf/articles
            """
        And there exists an "Article" document at "/cmf/articles/foo":
            | title | Article 1          |
            | body  | This is my article |
        And there exists an "Article" document at "/cmf/articles/foo/sub":
            | title | Sub-article 1      |
            | body  | This is my article |

    Scenario: Retrieving nested resources
        When I send a GET request to "/api/default/foo"
        Then the response should contain json:
            """
            {
                "repository_alias": "default",
                "repository_type": "doctrine_phpcr_odm",
                "payload_alias": null,
                "payload_type": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article",
                "path": "\/foo",
                "node_name": "foo",
                "label": "foo",
                "repository_path": "\/foo",
                "children": {
                    "sub": {
                        "repository_alias": "default",
                        "repository_type": "doctrine_phpcr_odm",
                        "payload_alias": null,
                        "payload_type": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article",
                        "path": "\/foo\/sub",
                        "node_name": "sub",
                        "label": "sub",
                        "repository_path": "\/foo\/sub",
                        "children": []
                    }
                }
            }
            """

    Scenario: Specifying a depth
        When I send a GET request to "/api/default/foo?depth=0"
        Then the response should contain json:
            """
            {
                "repository_alias": "default",
                "repository_type": "doctrine_phpcr_odm",
                "payload_alias": null,
                "payload_type": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article",
                "path": "\/foo",
                "node_name": "foo",
                "label": "foo",
                "repository_path": "\/foo",
                "children": {
                    "sub": []
                }
            }
            """
