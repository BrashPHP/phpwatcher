#include <stdint.h>
#include <stdlib.h>
#include <stdbool.h>

#define FFI_LIB "./watcher/out/this/Release/libwatcher-c.so"

struct wtr_watcher_event {
  int64_t effect_time;
  char const* path_name;
  char const* associated_path_name;
  int8_t effect_type;
  int8_t path_type;
};

/*  Ensure the user's callback can receive
    events and will return nothing. */
typedef void (* wtr_watcher_callback)(struct wtr_watcher_event event, void* context);

void* wtr_watcher_open(char const* const path, wtr_watcher_callback callback, void* context);

bool wtr_watcher_close(void* watcher);

typedef void (*handle_event_t)(struct wtr_watcher_event event, void *data);

extern handle_event_t handle_event_callback;


uintptr_t start_new_watcher(char const *const path);

int stop_watcher(uintptr_t watcher);