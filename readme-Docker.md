docker run -p 4000:80 --mount type=bind,source=/Users/rmayf/Pictures,target=/var/www/html/Home --mount type=bind,source=/Users/rmayf/Pictures/thumbs,target=/var/www/html/thumbs --sig-proxy=false mayf-photo
# -p 4000:80 maps containers port 80 to host's port 4000
# --mount mounts the directory from host to 'target' of container. Need to bind mount the directory containing the photos and the thumbnails
# --sig-proxy prevent a problem with apache crashing when resizing the tmux window due to SIGWINCH

# List running containers
docker container stats

# Shell into running container
docker exec -it <container_name> /bin/bash

# Build new image
docker build -t mayf-photo .
