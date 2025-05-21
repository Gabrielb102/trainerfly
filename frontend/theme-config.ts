export default {
  ui: {
    button: {
      slots: {
            base: 'rounded-full uppercase font-light justify-center',
      },
      variants: {
        size: {
          xs: {
            base: 'px-4',
          },
          sm: {
            base: 'px-4',
          },
          md: {
            base: 'px-6',
          },
          lg: {
            base: 'px-6',
          },
          xl: {
            base: 'px-8',
          }
        }
      }
    },
    input: {
      slots: {
        base: 'rounded-full',
      },
      variants: {
        variant: {
          soft: 'bg-zinc-200 hover:bg-zinc-300 focus:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 dark:focus:bg-zinc-700',
        }
      },
    }
  }
}
