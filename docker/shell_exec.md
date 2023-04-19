# Docker shell vs exec mode

### Docker uses two modes for some of its instructions from Dockerfile. Those are:

- shell mode
- exec mode

## Shell Mode

Commands are written without [] brackets and are run by the container's shell, such as /bin/sh -c

```
FROM alpine:latest

# /bin/sh -c 'echo $HOME'
RUN echo $HOME

# /bin/sh -c 'echo $PATH'
CMD echo $PATH
```

## Exec Mode

Commands are written with [] brackets and are run directly, not through a shell.

```
FROM alpine:latest

RUN ["pwd"]

CMD ["sleep", "1s"]
```

## Recommendations

These are the recommended forms to use for each instruction:

- RUN: shell form, because of the shell features
- ENTRYPOINT: exec form, because of the signal trapping
- CMD: exec form, because of the signal trapping






