Contributing to liquidchaos
===========================

So you wanna contribute to liquidchaos? Great! You don't even need to be a
programmer to contribute. You may:

- Submit patches
- Discuss features on our [forum]
- Report bugs through [issues]. 

[forum]: <https://github.com/zibonbadi/liquidchaos/discussions>
[issues]: <https://github.com/zibonbadi/liquidchaos/issues>


Bug reports & Discussions
-------------------------

Our general discussion policy:

- Keep it professional. All you'll do is embarrass yourself.
- Avoid duplicate threads.
- Don't expect an immediate answer. This is a community project from people
  across the globe and we need some sleep too.

Before filing an issue:

- Please look for similar already existing
  issues and comment on them whenever possible.
- Please make sure the problem can be replicated on a
  fresh system, you may have just misconfigured something.

Before making a fool of yourself on the forum, please keep in mind:


Contributing code
-----------------

The repository generally follows 3 types of branches:

- `main`: Stable release software; to be used by the public
- `dev`: Current development version; the place feature branches get
         merged to for testing before being transferred to `main`.
- `feature-branch`: Develop a new feature or fix

Feature branches are generally derived from `dev` and are where you spend
most of your development time. They should only be derived from `main` for
critical hotfixes and merging should be thoroughly discussed and tested beforehand.

If you contribute, please make sure to keep your additions separate from
the main code wherever possible. The most common practices for this are
distinct branches and the directory `contrib/`.

If you are trying to independently develop a feature, please send your
finished patches as a pull request to be checked by the maintenance team.

Just as the project itself, any contributions to liquidchaos will be
licensed under the GNU Affero General Public License 3 or later. All
contributors will be credited upon approval.

         
### The noob guide to GitHub contributions

If you have never contributed to a GitHub project
before, check out these guides to get started:

- [Finding ways to contribute to open source on GitHub](https://docs.github.com/en/get-started/exploring-projects-on-github/finding-ways-to-contribute-to-open-source-on-github)
- [Set up Git](https://docs.github.com/en/get-started/quickstart/set-up-git)
- [GitHub flow](https://docs.github.com/en/get-started/quickstart/github-flow)
- [Collaborating with pull requests](https://docs.github.com/en/github/collaborating-with-pull-requests)

If you followed the installation steps in `README.md`, you already have a
good base to develop a pull request. If you have been invited into the
repository's maintenance team, we suggest you to clone your working
repository using the SSH or GitHub client link; HTTP is too insecure.


### The noob guide to Composer

Usually the file `composer.lock` takes care that all of your dependencies
are of the right version when you run `composer install` to set up the
project, however if you need to update the dependencies you can do so by
simply running `composer update`.

Within the project the file `vendor/autoload.php` is responsible for
including all installed dependencies within your PHP environment.


### Writing tests

We're using [PHPUnit] for automated code testing. To run all existing
tests, simply run `composer run test`.

If you wanna write a test, the directory for that is `tests/`.

