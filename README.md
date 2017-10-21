# Symfony CMF Resource Rest Bundle

[![Latest Stable Version](https://poser.pugx.org/symfony-cmf/resource-rest-bundle/v/stable)](https://packagist.org/packages/symfony-cmf/resource-rest-bundle)
[![Latest Unstable Version](https://poser.pugx.org/symfony-cmf/resource-rest-bundle/v/unstable)](https://packagist.org/packages/symfony-cmf/resource-rest-bundle)
[![License](https://poser.pugx.org/symfony-cmf/resource-rest-bundle/license)](https://packagist.org/packages/symfony-cmf/resource-rest-bundle)

[![Total Downloads](https://poser.pugx.org/symfony-cmf/resource-rest-bundle/downloads)](https://packagist.org/packages/symfony-cmf/resource-rest-bundle)
[![Monthly Downloads](https://poser.pugx.org/symfony-cmf/resource-rest-bundle/d/monthly)](https://packagist.org/packages/symfony-cmf/resource-rest-bundle)
[![Daily Downloads](https://poser.pugx.org/symfony-cmf/resource-rest-bundle/d/daily)](https://packagist.org/packages/symfony-cmf/resource-rest-bundle)

Branch | Travis | Coveralls |
------ | ------ | --------- |
master | [![Build Status][travis_unstable_badge]][travis_unstable_link] | [![Coverage Status][coveralls_unstable_badge]][coveralls_unstable_link] |

This bundle is part of the [Symfony Content Management Framework (CMF)](http://cmf.symfony.com/) and licensed
under the [MIT License](LICENSE).

This Bundle provides a REST API to Puli resources as provided by the CmfResource component.


## Requirements

* PHP 7.0 / 7.1
* Symfony 2.8 / 3.1 / 3.2 / 3.3
* See also the `require` section of [composer.json](composer.json)

## Documentation

For the install guide and reference, see:

* [symfony-cmf/resource-rest-bundle Documentation](http://symfony.com/doc/master/cmf/bundles/resource-rest/index.html)

See also:

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
## Running the tests

### Behat
- Setup database: `./vendor/symfony-cmf/testing/bin/travis/phpcr_odm_doctrine_dbal.sh`
- Run server: `./vendor/symfony-cmf/testing/bin/server`
- Run tests: `./vendor/bin/behat`

## Support

For general support and questions, please use [StackOverflow](http://stackoverflow.com/questions/tagged/symfony-cmf).

## Contributing

Pull requests are welcome. Please see our
[CONTRIBUTING](https://github.com/symfony-cmf/symfony-cmf/blob/master/CONTRIBUTING.md)
guide.

Unit and/or functional tests exist for this bundle. See the
[Testing documentation](http://symfony.com/doc/master/cmf/components/testing.html)
for a guide to running the tests.

Thanks to
[everyone who has contributed](contributors) already.

## License

This package is available under the [MIT license](src/Resources/meta/LICENSE).

[travis_legacy_badge]: https://travis-ci.org/symfony-cmf/resource-rest-bundle.svg?branch=master
[travis_legacy_link]: https://travis-ci.org/symfony-cmf/resource-rest-bundle
[travis_stable_badge]: https://travis-ci.org/symfony-cmf/resource-rest-bundle.svg?branch=master
[travis_stable_link]: https://travis-ci.org/symfony-cmf/resource-rest-bundle
[travis_unstable_badge]: https://travis-ci.org/symfony-cmf/resource-rest-bundle.svg?branch=master
[travis_unstable_link]: https://travis-ci.org/symfony-cmf/resource-rest-bundle

[coveralls_legacy_badge]: https://coveralls.io/repos/github/symfony-cmf/resource-rest-bundle/badge.svg?branch=master
[coveralls_legacy_link]: https://coveralls.io/github/symfony-cmf/resource-rest-bundle?branch=master
[coveralls_stable_badge]: https://coveralls.io/repos/github/symfony-cmf/resource-rest-bundle/badge.svg?branch=master
[coveralls_stable_link]: https://coveralls.io/github/symfony-cmf/resource-rest-bundle?branch=master
[coveralls_unstable_badge]: https://coveralls.io/repos/github/symfony-cmf/resource-rest-bundle/badge.svg?branch=master
[coveralls_unstable_link]: https://coveralls.io/github/symfony-cmf/resource-rest-bundle?branch=master
