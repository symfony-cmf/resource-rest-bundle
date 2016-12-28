Feature: Security
    In order to deny API access to private files
    As a developer
    I need to be able to write security voters

    Background:
        Given the test application has the following configuration:
            """
            cmf_resource:
                repositories:
                    default:
                        type: doctrine_phpcr
                        basepath: /tests/cmf/articles
            """
        And there exists an "Article" document at "/cmf/private/foo":
            | title | Article 1          |
            | body  | This is my article |

    Scenario: Retrieve a protected resource
        When I send a GET request to "/api/phpcr_repo/cms/private/foo"
        Then the response code should be 401

    Scenario: Retrieve a protected non-existent resource
        When I send a GET request to "/api/phpcr_repo/cms/private/bar"
        Then the response code should be 401

    Scenario: Remove a protected resource
        When I send a DELETE request to "/api/phpcr_repo/cms/private/admin/something"
        Then the response code should be 401

    Scenario: Edit a resource
        When I send a PATCH request to "/api/phpcrodm_repo/cms/admin/file"
        Then the response code should be 401
