FROM node:bullseye-slim
ENV NODE_ENV=development
WORKDIR /var/www/html
COPY tracker .
COPY package*.json ./
RUN npm ci \
    && npm cache clean --force
ENV PATH /app/node_modules/.bin:$PATH
ENV TINI_VERSION v0.18.0
ADD https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini /tini
RUN chmod +x /tini
ENTRYPOINT ["/tini", "--"]
USER node
CMD [ "npm", "run", "dev", "--", "-o" ]