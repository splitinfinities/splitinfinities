# Build and tag:
```
docker build -t splitinfinities .
```

# Start:
```
docker run -d -p 8080:3000 \
-e DB_HOST='localhost:3000' \
-e DB_NAME='database' \
-e DB_USER='user' \
-e DB_PASSWORD='password' \
-e SENDGRID_API_KEY='asdf' \
-e SENDGRID_FROM_NAME='asdf' \
-e SENDGRID_FROM_EMAIL='asdf' \
splitinfinities
```

#### or

```
docker run -d -p 8080:3000 \
--env-file ./.env \
splitinfinities
```


then visit http://localhost:8080
