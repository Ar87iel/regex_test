git clean -fdx
git checkout -- *
git submodule foreach cd "$path"; git clean -fdx; git checkout -- *
