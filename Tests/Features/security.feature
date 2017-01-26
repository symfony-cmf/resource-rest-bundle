Feature: Security
    In order to deny API access to private files
    As a developer
    I need to be able to write security voters

    Background:
        Given the test application has the following configuration:
            """
            cmf_resource:
                repositories:
                    security:
                        type: doctrine_phpcr
                        basepath: /tests/cmf/articles
            """
        And there exists an "Article" document at "/private/foo":
            | title | Article 1          |
            | body  | This is my article |

    Scenario: Retrieve a protected resource
        When I send a GET request to "/api/security/private/foo"
        Then the response code should be 401

    Scenario: Retrieve a protected non-existent resource
        When I send a GET request to "/api/security/private/bar"
        Then the response code should be 401

    Scenario: Remove a protected resource
        When I send a DELETE request to "/api/security/private/admin/something"
        Then the response code should be 401

    Scenario: Edit a resource
        When I send a PATCH request to "/api/security/admin/file"
        Then the response code should be 401
