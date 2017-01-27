Feature: Filesystem resource repository
    In order to retrieve data from the resource webservice
    As a webservice user
    I need to be able to query the webservice

#    Background:
#        Given the test application has the following configuration:
#            """
#            cmf_resource:
#                repositories:
#                    default:
#                        type: puli/filesystem
#                        base_dir: "%kernel.root_dir%/Resources/views/snippets"
#            """
#        And there is a file named "%kernel.root_dir%/Resources/views/snippets/snippet1.html" with:
#            """
#            <h1>Snippet 1</h1>
#            """
#
#
#    Scenario: Retrieve filesystem resource
#        When I send a GET request to "/api/default/snippet1.html"
#        Then the response should contain json:
#            """
#            {
#                "repository_alias": "default",
#                "repository_type": "puli/filesystem",
#                "payload_alias": null,
#                "payload_type": null,
#                "path": "\/snippet1.html",
#                "node_name": "snippet1.html",
#                "label": "snippet1.html",
#                "repository_path": "\/snippet1.html",
#                "children": [],
#                "body": "<h1>Snippet 1</h1>"
#            }
#            """
