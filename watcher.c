#include "watcher.h"
#include "./watcher/out/this/Release/libwatcher-c.so"

void handle_event(struct wtr_watcher_event event, void *data) {
    printf("%s", event.path_name);
}

handle_event_t handle_event_callback = handle_event;

uintptr_t start_new_watcher(char const *const path) {
  void *watcher = wtr_watcher_open(path, handle_event_callback, NULL);
  if (watcher == NULL) {
    return 0;
  }
  return (uintptr_t)watcher;
}

int stop_watcher(uintptr_t watcher) {
  if (!wtr_watcher_close((void *)watcher)) {
    return 0;
  }
  return 1;
}

