# Contributing

If you're interested in contributing to Acuparse, the first step is joining the development mailing list.

Join the [mailing lists](https://lists.acuparse.com).

## Branches

- `stable`: Contains the most recent public stable release.
- `dev`: All development ends up in this branch, pending release.
Either directly or via Merge from a feature branch.

## Releases

Code released by merging `dev` into `stable` and tagging the merge commit.

## Versioning

Acuparse follows the Semantic Versioning guidelines. Releases will be numbered in the following format:

```<major>.<minor>.<patch>```

constructed within the following guidelines:

- Breaking backward compatibility or major SQL changes bumps the major (and resets the minor and patch).
- New additions and/or SQL changes without breaking backward compatibility bumps the minor (and resets the patch).
- Bug fixes, minor and misc changes bumps the patch.

For more information on SemVer, visit [semver.org](http://www.semver.org).
