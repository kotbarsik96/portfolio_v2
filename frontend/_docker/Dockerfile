FROM node:18.13.0-alpine

WORKDIR /var/www/portfolio/frontend

COPY ./portfolio/frontend/package*.json ./

RUN npm install

# при переключении build/dev и обратно, выполнить docker rmi portfolio-frontend. Также изменить:
# FRONTEND_URL и SANCTUM_STATEFUL_DOMAINS в portfolio/backend/.env, прописав порт 5173,
# раскомментировать ports в docker-compose.yml
CMD ["npm", "run", "build"]
# CMD ["npm", "run", "dev"]