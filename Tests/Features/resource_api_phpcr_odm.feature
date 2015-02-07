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
                    article: { repository: doctrine_phpcr_odm, type: "Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Resources\TestBundle\Document\Article" }
            """


    Scenario: Retrieve a PHPCR-ODM resource with children
        Given there exists a "Article" document at "/cmf/articles/foo":
            | title | Article 1 |
            | body | This is my article |
        And there exists a "Article" document at "/cmf/articles/foo/bar":
            | title | Article 2 |
            | body | This is my second article |
        And there exists a "Article" document at "/cmf/articles/foo/boo":
            | title | Article 2 |
            | body | This is my third article |
        Then I send a GET request to "/api/phpcrodm_repo/foo"
        And print response
        And the response code should be 200
        And the response should contain json:
            """
            {
                "_links": {
                    "self": {
                        "href": "/api/phpcrodm_repo/foo"
                    }
                },
                "children": {
                    "bar": {
                        "_links": {
                            "self": {
                                "href": "/api/phpcrodm_repo/foo/bar"
                            }
                        },
                        "children": [],
                        "document": {
                            "_links": {
                                "self": {
                                    "href": "/path/to/this"
                                }
                            },
                            "body": "This is my second article",
                            "id": "/tests/cmf/articles/foo/bar",
                            "title": "Article 2"
                        },
                        "path": "/foo/bar",
                        "repo_path": "/foo/bar"
                    },
                    "boo": {
                        "_links": {
                            "self": {
                                "href": "/api/phpcrodm_repo/foo/boo"
                            }
                        },
                        "children": [],
                        "document": {
                            "_links": {
                                "self": {
                                    "href": "/path/to/this"
                                }
                            },
                            "body": "This is my third article",
                            "id": "/tests/cmf/articles/foo/boo",
                            "title": "Article 2"
                        },
                        "path": "/foo/boo",
                        "repo_path": "/foo/boo"
                    }
                },
                "document": {
                    "_links": {
                        "self": {
                            "href": "/path/to/this"
                        }
                    },
                    "body": "This is my article",
                    "id": "/tests/cmf/articles/foo",
                    "title": "Article 1"
                },
                "path": "/foo",
                "repo_path": "/foo"
            }
            """
