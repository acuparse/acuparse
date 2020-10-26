##
# Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
# @copyright Copyright (C) 2015-2020 Maxwell Power
# @author Maxwell Power <max@acuparse.com>
# @link http://www.acuparse.com
# @license AGPL-3.0+
#
# This code is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published
# by the Free Software Foundation, either version 3 of the License, or
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this code. If not, see <http://www.gnu.org/licenses/>.
##

##
# File: Dockerfile
# Acuparse Dockerfile
##

FROM php:apache

ARG BUILD_DATE
ARG VCS_REF
ARG BUILD_VERSION
ARG BUILD_BRANCH
ARG ACUPARSE_DIR='/opt/acuparse'

LABEL MAINTAINER="hello@acuparse.com" \
org.label-schema.schema-version="1.0" \
org.label-schema.build-date=$BUILD_DATE \
org.label-schema.name="acuparse/acuparse" \
org.label-schema.description="Acuparse Weather Data Processing" \
org.label-schema.url="https://www.acuarse.com/" \
org.label-schema.vcs-url="https://gitlab.com/acuparse/acuparse" \
org.label-schema.vcs-ref=$VCS_REF \
org.label-schema.version=$BUILD_VERSION \
org.label-schema.usage="https://docs.acuparse.com/DOCKER" \
org.label-schema.vendor="Acuparse"

RUN echo "Process Updates and Install PACKAGES" \
&& export DEBIAN_FRONTEND=noninteractive \
&& apt-get update -qq \
&& apt-get dist-upgrade -yqq \
&& apt-get install mariadb-client rsyslog python-certbot-apache cron -yqq \
&& docker-php-ext-install mysqli \
&& docker-php-ext-enable mysqli \
&& apt-get autoremove --purge -yqq \
&& apt-get clean -yqq

COPY .. "${ACUPARSE_DIR}"
WORKDIR "${ACUPARSE_DIR}"

RUN echo "Copy and then run Acuparse BUILD" \
&& mv .docker/* /usr/local/bin/ \
&& chmod +x /usr/local/bin/docker-build \
&& docker-build

ENTRYPOINT ["docker-entrypoint"]
CMD ["acuparse"]

EXPOSE 443
