FROM nginx:stable-alpine

RUN rm /etc/nginx/conf.d/default.conf

COPY ./frontend/build /usr/share/nginx/html

COPY nginx/nginx.conf /etc/nginx/conf.d

EXPOSE 80

