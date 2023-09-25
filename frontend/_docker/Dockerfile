FROM node:18.13.0-alpine

WORKDIR /var/www/frontend

COPY ./frontend/package*.json ./

RUN npm install

CMD ["npm", "run", "dev"]