# Tmux CheatSheet

## Basic Commands

- tmux list-sessions
- tmux new-session -s *session_name* -n *window_name*
- tmux attach-session -t *session_name*
- tmux kill-session -t *session_name*
- tmux set-option -g mouse on
- tmux kill-server
- tmux list-clients

## Action Commands

- ctrlb c -create new window
- ctrlb , -rename window
- ctrlb d - detach from session
- ctrlb w - list windows
- ctrlb s - list sessions
- ctrlb num - select window number
- ctrlb l - toggle last active window
- ctrlb ; - toggle last active pane
- ctrlb n - next window
- ctrlb p - previous window
- ctrlb $ - rename session
- ctrlb & - kill window
- ctrlb x - kill pane
- ctrlb " - split horizontaly
- ctrlb % - split vertically
- ctrlb {} - move pane left/right
- ctrlb space - autoarrange panes
- ctrlb q - show pane numbers
- ctrlb up/down/left/right - move through panes
- ctrlb ctrl+up/down/left/right - resize panes
- ctrlb ! - break out pane
- ctrlb ) - next session
- ctrlb ( - previous session
- ctrlb t - show clock
- ctrlb : - command mode


