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



            """


    Scenario: Retrieve PHPCR resource with children
        Given there exists a "Article" document at "/cmf/articles/foo":
            | title | Article 1 |
            | body | This is my article |
        And there exists a "Article" document at "/cmf/articles/foo/bar":
            | title | Article 2 |
            | body | This is my second article |
        And there exists a "Article" document at "/cmf/articles/foo/boo":
            | title | Article 2 |
            | body | This is my third article |
        Then I send a GET request to "/api/phpcr_repo/foo"
        And print response
        And the response should contain json:
            """
            {
                "_links": {
                    "self": {
                        "href": "/api/phpcr_repo/foo"
                    }
                },
                "children": {
                    "bar": {
                        "_links": {
                            "self": {
                                "href": "/api/phpcr_repo/foo/bar"
                            }
                        },
                        "children": [],
                        "node": {
                            "body": "This is my second article",
                            "jcr:mixinTypes": [
                                "phpcr:managed"
                            ],
                            "jcr:primaryType": "nt:unstructured",
                            "phpcr:class": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article",
                            "phpcr:classparents": [],
                            "title": "Article 2"
                        },
                        "path": "/foo/bar",
                        "repo_path": "/foo/bar"
                    },
                    "boo": {
                        "_links": {
                            "self": {
                                "href": "/api/phpcr_repo/foo/boo"
                            }
                        },
                        "children": [],
                        "node": {
                            "body": "This is my third article",
                            "jcr:mixinTypes": [
                                "phpcr:managed"
                            ],
                            "jcr:primaryType": "nt:unstructured",
                            "phpcr:class": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article",
                            "phpcr:classparents": [],
                            "title": "Article 2"
                        },
                        "path": "/foo/boo",
                        "repo_path": "/foo/boo"
                    }
                },
                "node": {
                    "body": "This is my article",
                    "jcr:mixinTypes": [
                        "phpcr:managed"
                    ],
                    "jcr:primaryType": "nt:unstructured",
                    "phpcr:class": "Symfony\\Cmf\\Bundle\\ResourceRestBundle\\Tests\\Resources\\TestBundle\\Document\\Article",
                    "phpcr:classparents": [],
                    "title": "Article 1"
                },
                "path": "/foo",
                "repo_path": "/foo"
            }
            """
