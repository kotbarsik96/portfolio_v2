FROM node:lts-alpine

WORKDIR /var/www/frontend

COPY package*.json ./

RUN npm install

RUN npm run build