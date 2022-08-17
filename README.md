# Portal Platform Coop

### Technologies

- MariaDB 10.8.2

- WordPress 6.0.1
 

### Development

From the **.env.example** file, create a **.env** file, change the variable values, if necessary.

```bash
# Upload docker containers
$ docker-compose -f docker-compose-local.yaml up -d
```

```bash
# Monitor logs
$ docker-compose -f docker-compose-local.yaml logs -f
```

```bash
# stop containers
$ docker-compose -f docker-compose-local.yaml stop
```

```bash
# Remove containers
$ docker-compose -f docker-compose-local.yaml down
```

```bash
# Remove containers and volumes (erases all docker data!)
$ docker-compose -f docker-compose-local.yaml down --remove-orphans -v
```

### Depoloy Development/Approval/Production
```bash
# Upload docker containers with proxy and let's encrypt
$ docker-compose up -d
```
> Don't forget to inform the domain in the **WORDPRESS_VIRTUAL_HOST** variable in the **.env** file
