# Using GIT

## Setup your own public GitHub repository

Your first step is to establish a public repository from which you can pull your work into the master repository.

 1. Setup a [GitHub account](https://github.com/), if you haven't yet
 2. Fork the [proophessor-do-symfony repository](https://github.com/prooph/proophessor-do-symfony)
 3. Clone your fork locally and enter it (use your own GitHub username in the statement below)

    ```sh
    $ git clone git@github.com:[your username]/proophessor-do-symfony.git
    $ cd proophessor-do-symfony
    ```

 4. Add a remote to the canonical `proophessor-do-symfony` repository, so you can keep your fork up-to-date:

    ```sh
    $ git remote add upstream https://github.com/prooph/proophessor-do-symfony.git
    $ git fetch upstream
    ```

## Keeping Up-to-Date

Periodically, you should update your fork to match the canonical `proophessor-do-symfony` repository. we have
added a remote to the `proophessor-do-symfony` repository, which allows you to do the following:

```sh
$ git checkout master
$ git pull upstream master
- OPTIONALLY, to keep your remote up-to-date -
$ git push origin
```

If you're tracking other branches -- for example, the *develop* branch, where new feature development occurs --
you'll want to do the same operations for that branch; simply substitute  "develop" for "master".

## Working on proophessor-do-symfony

When working on `proophessor-do-symfony`, we recommend you do each new feature or bugfix in a new branch. This simplifies the
task of code review as well as of merging your changes into the canonical repository.

A typical work flow will then consist of the following:

 1. Create a new local branch based off your master branch.
 2. Switch to your new local branch. (This step can be combined with the previous step with the use of `git checkout -b`.)
 3. Do some work, commit, repeat as necessary.
 4. Push the local branch to your remote repository.
 5. Send a pull request.

The mechanics of this process are actually quite trivial. Below, we will create a branch for fixing an issue in the tracker.

```sh
$ git checkout -b 3452
Switched to a new branch '3452'
```
... do some work ...

```sh
$ git commit
```
... write your log message ...

```sh
$ git push origin HEAD:3452
Counting objects: 38, done.
Delta compression using up to 2 threads.
Compression objects: 100$ (18/18), done.
Writing objects: 100$ (20/20), 8.19KiB, done.
Total 20 (delta 12), reused 0 (delta 0)
To ssh://git@github.com/prooph/proophessor-do-symfony.git
   g5342..9k3532  HEAD -> master
```

You can do the pull request from GitHub. Navigate to your repository, select the branch you just created, and then
select the "Pull Request" button in the upper right. Select the user *prooph* as the recipient.

### Branch to issue the pull request

Which branch should you issue a pull request against?

- For fixes against the stable release, issue the pull request against the *master* branch.
- For new features, or fixes that introduce new elements to the public API
  (such as new public methods or properties), issue the pull request against the *develop* branch.

## Branch Cleanup

As you might imagine, if you are a frequent contributor, you'll start to get a ton of branches both locally and on
your remote.

Once you know that your changes have been accepted to the master repository, we suggest doing some cleanup of these
branches.

 -  Local branch cleanup

    ```sh
    $ git branch -d <branchname>
    ```

 -  Remote branch removal

    ```sh
    $ git push origin :<branchname>
    ```


## Feed and emails

RSS feeds may be found at:

`https://github.com/prooph/proophessor-do-symfony/commits/<branch>.atom`

where &lt;branch&gt; is a branch in the repository.

To subscribe to git email notifications, simply watch or fork the `proophessor-do-symfony` repository on GitHub.
