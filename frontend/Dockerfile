FROM node:alpine

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

COPY .env.prod .env

RUN npm run build

CMD npx serve -s build
