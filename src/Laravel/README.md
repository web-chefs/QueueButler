# Laravel Copies

In an attempt to support a feature parity with Laravel we first extended from
Laravel's own framework classes.

This very quickly lead to version in compatibilities where class interfaces
changed breaking compatibility.

After considering a few options it was decided to copy the latest versions of
Laravel's classes here in the "Laravel" folder and then extend from them our own
customizations.

This way we can support a the same features of Laravel but maintain a stable
class interface across multiple Laravel versions.
