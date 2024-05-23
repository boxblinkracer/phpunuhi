# Contributing Guidelines

*Pull requests, bug reports, and all other forms of contribution are welcomed and highly encouraged!* :octocat:

## Creating Pull Requests

I'm super happy to accept pull requests! Here's a quick guide on how to make my life easier when reviewing it.

### Running Tests (locally)

I did my best to create a good CI pipeline to avoid as many problems as possible.
To already check if your code is good to go, you can run the following command locally before doing a Pull Request.

```bash
make pr
```

This will run the **fixers** and all **tests** from the pipeline.
So it can be that your GIT tool shows changes afterwards from the fixers, just commit these to your branch.

If this command runs completely through, the pipeline on Github will also pass.

### Changelog

Please create a changelog entry for your changes. This can be done by adding a line to the `CHANGELOG.md` file.
This helps me to keep track of what has changed and to create a new release later on.

THANK YOU FOR YOUR HELP
