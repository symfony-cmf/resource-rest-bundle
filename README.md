# Symfony CMF Resource REST API Bundle

[![Build Status](https://travis-ci.org/symfony-cmf/resource-rest-bundle.svg?branch=master)](https://travis-ci.org/symfony-cmf/resource-rest-bundle)
[![StyleCI](https://styleci.io/repos/29090266/shield)](https://styleci.io/repos/29090266)
[![Latest Stable Version](https://poser.pugx.org/symfony-cmf/resource-rest-bundle/version.png)](https://packagist.org/packages/symfony-cmf/resource-rest-bundle)
[![Total Downloads](https://poser.pugx.org/symfony-cmf/resource-rest-bundle/d/total.png)](https://packagist.org/packages/symfony-cmf/resource-rest-bundle)

This Bundle provides a REST API to [Puli](https://github.com/puliphp/puli)
resources as provided by the
[CmfResource](https://github.com/symfony-cmf/Resource) component.

## Requirements 

* Symfony 2.8+
* See also the `require` section of [composer.json](composer.json)

## Documentation

Not yet.

* [All Symfony CMF documentation](http://symfony.com/doc/master/cmf/index.html) - complete Symfony CMF reference
* [Symfony CMF Website](http://cmf.symfony.com/) - introduction, live demo, support and community links

## Example

````bash
$ curl http://localhost:8000/api/phpcrodm_repo/foo | python -m json.tool
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
````


## Contributing

Pull requests are welcome. Please see our
[CONTRIBUTING](https://github.com/symfony-cmf/symfony-cmf/blob/master/CONTRIBUTING.md)
guide.

Unit and/or functional tests exist for this bundle. See the
[Testing documentation](http://symfony.com/doc/master/cmf/components/testing.html)
for a guide to running the tests.

Thanks to
[everyone who has contributed](https://github.com/symfony-cmf/ResourceRestBundle/contributors) already.
## Running the tests

### Behat
- Setup database: `./vendor/symfony-cmf/testing/bin/travis/phpcr_odm_doctrine_dbal.sh`
- Run server: `./vendor/symfony-cmf/testing/bin/server`
- Run tests: `./vendor/bin/behat`
