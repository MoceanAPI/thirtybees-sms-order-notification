# Remove existing build zip file
rm moceanapinotify.zip

# Rsync contents of folder to new directory that we will use for the build
rsync -Rr ./* ./moceanapinotify

# Remove directories and files from newly created directory, that we won't need in final build
rm ./moceanapinotify/build.sh

# Zip contents of newly created directory
zip -r moceanapinotify.zip ./moceanapinotify

# Clean up
rm -rf moceanapinotify