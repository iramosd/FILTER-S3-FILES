FROM ubuntu:24.04

WORKDIR /var/www/html

RUN apt update && \ 
    apt install -y software-properties-common && add-apt-repository ppa:ondrej/php -y && \
    apt install -y php8.3 php8.3-mcrypt php8.3-gd php8.3-curl php8.3-mysql php8.3-zip php8.3-xml php8.3-soap php8.3-intl php8.3-mbstring php8.3-bcmath && \
    apt install -y composer dos2unix && composer config --global process-timeout 1500 && \
    apt remove apache2 -y && apt autoremove -y

COPY docker-scripts/php-setup-project.sh /usr/local/bin/php-setup-project.sh

RUN dos2unix /usr/local/bin/php-setup-project.sh

RUN chmod +x /usr/local/bin/php-setup-project.sh