#
# Centos 7 image for coddns v2
#
# Date: 2017/05/20
# Author: Fco de Borja Sanchez <fborja.sanchezs@gmail.com>
#
#
# Build: docker build --rm --no-cache -t coddns/coddns:v2-prealpha  
# Run:   docker run --privileged --name httpd -v /sys/fs/cgroup:/sys/fs/cgroup:ro -p 80:80 -d coddns/coddns:2.0-prealpha
#
FROM centos/systemd

MAINTAINER fborja.sanchezs@gmail.com
ENV container=docker

# Deploy files
COPY . /tmp/

# Default workdir
WORKDIR /opt/coddns/

# install and enable httpd
RUN yum install -y epel-release  && \
    rpm -Uvh /tmp/docker/remi-release-7.rpm && \
    rpm -ivh /tmp/docker/mysql-community-release-el7-5.noarch.rpm && \
    yum install -y httpd php php-pecl-ssh2 php-mysql which nmap sudo && \
    systemctl enable httpd.service

# install and enable mysqld
RUN yum install -y mysql-server && \
    systemctl enable mysqld.service ; \
    sudo -u mysql /usr/sbin/mysqld --initialize-insecure ;\
    exit 0

# install and enable bind
RUN yum install -y bind bind-utils && \
    systemctl enable named.service


RUN mkdir -p /opt/coddns/spool && \
    mv /tmp/coddns_console/coddns /var/www/html/ && \
    chown -R apache. /var/www/html/ && \
    mv /tmp/coddns_core/opencore/* /opt/coddns/ && \
    ln -s /opt/coddns/dnsmgr.sh /usr/bin/dnsmgr && \
    echo '<meta http-equiv="refresh" content="0; url=/coddns/index.php">' > /var/www/html/index.html && \
    systemctl enable mysqld.service && \
    systemctl enable named.service

# Final update & mysql fix
RUN yum update -y

# Exposing ports for: HTTP, HTTPS, Bind
EXPOSE 80 443 53/udp

#RUN cp /tmp/docker/entrypoint.sh / && \
#    chmod +x /entrypoint.sh

