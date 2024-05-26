# Contributing Guidelines

*Pull requests, bug reports, and all other forms of contribution are welcomed and highly encouraged!* :octocat:

<!-- TOC -->
* [Contributing Guidelines](#contributing-guidelines)
  * [1. Starting Dev Environment](#1-starting-dev-environment)
  * [2. Make Commands](#2-make-commands)
  * [3. Code Complexity](#3-code-complexity)
  * [4. Creating Pull Requests](#4-creating-pull-requests)
    * [4.1 Running Tests (locally)](#41-running-tests-locally)
    * [4.2 Changelog](#42-changelog)
<!-- TOC -->

## 1. Starting Dev Environment

The project is using Docker as a dev environment.
However, you can use anything you want.

If you want to use the built-in environment, make sure
to install Docker on your machine and then just run this command in the **devops** folder.

```bash
make run
```

## 2. Make Commands

I use **makefiles** as abstraction layer.
So you find all kinds of commands that you can run.
Just run this command (recommended inside the container of the Dev-Environment) and hit **ENTER**.
Then you see a **list of commands** that you can use.

```bash
make [ENTER]

# sample of commands
make dev
make phpunit
make build
```

## 3. Code Complexity

Please make sure the code is as **simple as possible**.
Try to code as if it would just work, and just **fail-fast** if something is breaking.
Don't try to do all kinds of try/catch blocks that increases complexity and chances for side effects.

Also try to create **comments** where things might not be clear.

## 4. Creating Pull Requests

I'm super happy to accept pull requests! Here's a quick guide on how to make my life easier when reviewing it.

### 4.1 Running Tests (locally)

I did my best to create a good CI pipeline to avoid as many problems as possible.
To already check if your code is good to go, you can run the following command locally before doing a Pull Request.

```bash
make pr
```

This will run the **fixers** and all **tests** from the pipeline.
So it can be that your GIT tool shows changes afterwards from the fixers, just commit these to your branch.

If this command runs completely through, the pipeline on Github will also pass.

### 4.2 Changelog

Please create a changelog entry for your changes. This can be done by adding a line to the `CHANGELOG.md` file.
This helps me to keep track of what has changed and to create a new release later on.

THANK YOU FOR YOUR HELP
