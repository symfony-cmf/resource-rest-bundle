Feature: PHPCR-ODM resource repository
    In order to retrieve data from the resource webservice
    As a webservice user
    I need to be able to query the webservice

    Background:
        Given the test application has the following configuration:
            """
            cmf_resource:
                description: { enhancers: [dummy] }
                repositories:
                    phpcrodm_repo:
                        type: doctrine/phpcr-odm
                        basepath: /tests/cmf/articles

            cmf_resource_rest:
                payload_alias_map:
                    article:
                        repository: doctrine/phpcr-odm
                        type: "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Fixtures\\App\\Document\\Article"
                security:
                    access_control:
                        - { pattern: '^/', require: IS_AUTHENTICATED_ANONYMOUSLY }
            """


    Scenario: Retrieve a PHPCR-ODM resource
        Given there exists an "Article" document at "/cmf/articles/foo":
            | title | Article 1          |
            | body  | This is my article |
        When I send a GET request to "/api/phpcrodm_repo/foo"
        Then the response code should be 200
        And the response should contain json:
            """
            {
                "repository_alias": "phpcrodm_repo",
                "repository_type": "doctrine/phpcr-odm",
                "payload_alias": "article",
                "payload_type": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Fixtures\\App\\Document\\Article",
                "path": "\/foo",
                "node_name": "foo",
                "label": "foo",
                "repository_path": "\/foo",
                "children": [],
                "descriptors": {
                    "name_reverse": "oof"
                }
            }
            """

    Scenario: Retrieve a PHPCR-ODM resource with children
        Given there exists an "Article" document at "/cmf/articles/foo":
            | title | Article 1          |
            | body  | This is my article |
        And there exists an "Article" document at "/cmf/articles/foo/bar":
            | title | Article child          |
            | body  | There are many like it |
        And there exists an "Article" document at "/cmf/articles/foo/boo":
            | title | Article child        |
            | body  | But this one is mine |
        When I send a GET request to "/api/phpcrodm_repo/foo"
        Then the response code should be 200
        And the response should contain json:
            """
            {
                "repository_alias": "phpcrodm_repo",
                "repository_type": "doctrine/phpcr-odm",
                "payload_alias": "article",
                "payload_type": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Fixtures\\App\\Document\\Article",
                "path": "\/foo",
                "node_name": "foo",
                "label": "foo",
                "repository_path": "\/foo",
                "children": {
                    "bar": {
                        "repository_alias": "phpcrodm_repo",
                        "repository_type": "doctrine/phpcr-odm",
                        "payload_alias": "article",
                        "payload_type": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Fixtures\\App\\Document\\Article",
                        "path": "/foo/bar",
                        "node_name": "bar",
                        "label": "bar",
                        "repository_path": "/foo/bar",
                        "children": [ ],
                        "descriptors": { "name_reverse": "rab" }
                    },
                    "boo": {
                        "repository_alias": "phpcrodm_repo",
                        "repository_type": "doctrine/phpcr-odm",
                        "payload_alias": "article",
                        "payload_type": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Fixtures\\App\\Document\\Article",
                        "path": "/foo/boo",
                        "node_name": "boo",
                        "label": "boo",
                        "repository_path": "/foo/boo",
                        "children": [ ],
                        "descriptors": { "name_reverse": "oob" }
                    }
                },
                "descriptors": {
                    "name_reverse": "oof"
                }
            }
            """

    Scenario: Rename a PHPCR-ODM resource
        Given there exists an "Article" document at "/cmf/articles/foo":
            | title | Article 1          |
            | body  | This is my article |
        When I send a PATCH request to "/api/phpcrodm_repo/foo" with body:
            """
            [{"operation": "move", "target": "/foo-bar"}]
            """
        Then the response code should be 200
        And there is an "Article" document at "/cmf/articles/foo-bar":
            | title | Article 1          |
            | body  | This is my article |

    Scenario: Move a PHPCR-ODM resource
        Given there exists an "Article" document at "/cmf/articles/foo":
            | title | Article 1          |
            | body  | This is my article |
        And there exists an "Article" document at "/cmf/articles/bar":
            | title | Article 2   |
            | body  | Another one |
        When I send a PATCH request to "/api/phpcrodm_repo/foo" with body:
            """
            [{"operation": "move", "target": "/bar/foo"}]
            """
        Then the response code should be 200
        And the response should contain json:
            """
            {
                "repository_alias": "phpcrodm_repo",
                "repository_type": "doctrine/phpcr-odm",
                "payload_alias": "article",
                "payload_type": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Fixtures\\App\\Document\\Article",
                "path": "\/bar\/foo",
                "node_name": "foo",
                "label": "foo",
                "repository_path": "\/bar\/foo",
                "children": []
            }
            """
        And there is an "Article" document at "/cmf/articles/bar/foo":
            | title | Article 1          |
            | body  | This is my article |

    Scenario: Remove a PHPCR-ODM resource
        Given there exists an "Article" document at "/cmf/articles/foo":
            | title | Article 1          |
            | body  | This is my article |
        When I send a DELETE request to "/api/phpcrodm_repo/foo"
        Then the response code should be 204
        And there is no "Article" document at "/cmf/articles/foo"
