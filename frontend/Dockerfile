FROM node:20-alpine AS build-stage

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

RUN npm run build

RUN ls -la dist/
RUN ls -la dist/assets/

FROM nginx:stable-alpine AS production-stage

COPY --from=build-stage /app/dist/ /usr/share/nginx/html/

RUN ls -la /usr/share/nginx/html/
RUN ls -la /usr/share/nginx/html/assets/

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]