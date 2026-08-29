# Contributing

Thank you for considering contributing to PHP Google Maps URLs!

We accept contributions via pull requests on [GitHub]. Please review these guidelines before submitting any pull
requests.

## Guidelines

* Code style is enforced with [Pint](https://laravel.com/docs/pint); run `make lint` before committing.
* One pull request per feature (send multiple if you want to do more than one thing).
* Add tests if you've added something new (ensure that the current tests pass).
* Send a coherent commit history (make sure each individual commit in your pull request is meaningful).
* Document any change in behaviour (make sure the `README.md` is kept up-to-date).
* Strictly follow our [Git Commit Guidelines](#git-commit-guidelines)!
* Please remember that we follow [SemVer](http://semver.org/).

### Git Commit Guidelines

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification for our git commit messages. A
consistent format keeps the commit history readable and makes it easy to generate the changelog.

#### Commit Message Format

```html
<type>(<scope>): <subject>
<BLANK LINE>
<body>
<BLANK LINE>
<footer>
```

> Any line of the commit message cannot be longer than 100 characters!
> This allows the message to be easier to read on GitHub as well as in various git tools.

##### Type

Must be one of the following:

* **feat:** a new feature
* **fix:** a bug fix
* **docs:** documentation only changes
* **style:** changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc.)
* **refactor:** a code change that neither fixes a bug nor adds a feature
* **perf:** a code change that improves performance
* **test:** adding missing tests or correcting existing ones
* **build:** changes to the build process, the dependencies, or the development tooling
* **ci:** changes to the GitHub Actions workflows
* **chore:** anything else that touches neither `src` nor `tests`

##### Scope

The scope could be anything specifying the place of the commit change.

##### Subject

The subject contains a succinct description of the change:

* use the imperative, present tense: "change" not "changed" nor "changes"
* don't capitalize first letter
* no dot (.) at the end

##### Body

Just as in the **subject**, use the imperative, present tense: "change" not "changed" nor "changes" The body should
include the motivation for the change and contrast this with previous behavior.

##### Footer

The footer is optional and may contain one or more footers, each on its own line. Use it to reference GitHub issues
that this commit closes (e.g. `Closes #123`) and to describe breaking changes.

A breaking change must be signalled in one of two ways:

* append a `!` after the type/scope, e.g. `fix(display-map)!: return the zoom as an integer`, or
* start a footer line with `BREAKING CHANGE:` followed by a description of what changed.

Both may be combined. The description explains what breaks and what community users must do to adapt, and is
highlighted in the changelog.

```
fix(display-map)!: return the zoom as an integer

BREAKING CHANGE: `DisplayMapAction::getZoom()` now returns an `int` instead of a `string`. Update any code that
relies on the previous return type.
```

## Local Development

Development runs entirely inside Docker, so you don't need PHP or [Composer](https://getcomposer.org/) installed
locally. Everything is driven through the `Makefile`. Run `make` (or `make help`) at any time to see the available
targets.

Before doing anything else, build the images and install the dependencies:

```bash
make build
make install
```

The images use PHP 8.5, which is also the minimum version this package requires. To build against a newer version,
pass it through `ARGS`:

```bash
make build ARGS="--build-arg PHP=8.6"
```

### Common tasks

| Command         | Description                                               |
|-----------------|-----------------------------------------------------------|
| `make test`     | Run the [Pest](https://pestphp.com/) test suite           |
| `make coverage` | Run the test suite with code coverage (written to `build/`) |
| `make lint`     | Fix code style with [Pint](https://laravel.com/docs/pint) |
| `make types`    | Run the [PHPStan](https://phpstan.org/) static analysis   |

Each target is a thin wrapper around a Composer script executed in the `composer` container, e.g. `make test` runs
`docker compose run --rm composer test`. If you prefer, you can invoke those scripts directly:

```bash
docker compose run --rm composer test
```

When you make a pull request, the tests will be automatically run again
by [GitHub Actions](https://github.com/cyrildewit/php-maps-urls/actions).

[GitHub]: https://github.com/cyrildewit/php-maps-urls/pulls
