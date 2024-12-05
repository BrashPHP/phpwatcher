#include <stdbool.h>
#include <stdint.h>


static const int8_t WTR_WATCHER_EFFECT_RENAME = 0;
static const int8_t WTR_WATCHER_EFFECT_MODIFY = 1;
static const int8_t WTR_WATCHER_EFFECT_CREATE = 2;
static const int8_t WTR_WATCHER_EFFECT_DESTROY = 3;
static const int8_t WTR_WATCHER_EFFECT_OWNER = 4;
static const int8_t WTR_WATCHER_EFFECT_OTHER = 5;

static const int8_t WTR_WATCHER_PATH_DIR = 0;
static const int8_t WTR_WATCHER_PATH_FILE = 1;
static const int8_t WTR_WATCHER_PATH_HARD_LINK = 2;
static const int8_t WTR_WATCHER_PATH_SYM_LINK = 3;
static const int8_t WTR_WATCHER_PATH_WATCHER = 4;
static const int8_t WTR_WATCHER_PATH_OTHER = 5;


struct wtr_watcher_event {
  int64_t effect_time;
  char const* path_name;
  char const* associated_path_name;
  int8_t effect_type;
  int8_t path_type;
};

typedef void (* wtr_watcher_callback)(struct wtr_watcher_event event, void* context);

void* wtr_watcher_open(char const* const path, wtr_watcher_callback callback, void* context);

bool wtr_watcher_close(void* watcher);
