# uParts Programming Exercise

Author: Zachary May (zach@sector42.net)

Note: The project uses some PHP 7.0 features, so a local installation (or [Docker image](https://hub.docker.com/_/php/))
with PHP 7.0+ is required.

## Running the Project

- Install the project's dependencies by running `composer install` in the project root.
- Run PHP's built-in web server (`php -S`) in the project root for testing the project.
- The project should now be accessible at the specified address/port.

## Organization

The project uses the Slim framework. While this simple exercise does not require routing, it made sense to use a
concrete framework to demonstrate how I would organize a PHP codebase.

Slim's dependency injection framework allows us to specify various dependencies and configuration options in isolation
from the classes that use them.

The `index.php` file sets up this dependency injection framework and bootstraps Slim to handle requests.

We use Twig as a simple templating system. Note the use of template inheritance to abstract basic page structure from
the specific content of the vehicle fitments tool.

Handler classes perform the role of "controllers" in the model-view-controller pattern. Slim is responsible for
instantiating a handler class based on the request's route, injecting the dependency injection container in the
constructor. Importantly, handlers are written against a standard interface for DI containers, rather than a
concrete implentation that unnecessarily increases the coupling in our code base.

Interaction with the TireSync API is wrapped with the `TireSync` interface and its default implementation `TireSyncImpl`.
This interface exposes several methods for issuing requests to the TireSync API. When bootstrapping the application,
we declare such a client is available in the DI container (and how to instantiate it), so `FitmentHandler` can make use
of it. In this way, we can implement (and unit test!) the TireSync client independently of the handler that will use it.

`TireSyncImpl` itself makes use of its own dependency, `HttpClient` a very simple interface for executing HTTP `GET`
requests.  In this case, the one implementor of `HttpClient` is `JsonHttpClient`, which uses `json_decode` to parse
the response body as JSON and return a PHP associative array as a result.

One nice benefit of using a dependency injection framework is that classes need not be concerned about their 
transitive dependencies, i.e., the dependencies of their dependencies. `FixmentHandler` needs a `TireSync` client,
but does not need to worry about how to get the `HttpClient` it would need to make one.

Unit tests live in the `tests/` directory, which contains a directory structure that mirrors the production code
structure in `src/`. The unit tests use the PHPUnit framework in addition to the Mockery library for creating
mock objects.

Mock objects are a powerful testing tool that extend the idea of stub objects that just accept method calls
and dumbly return fake results. Instead, mocks actually allow you to make assertions that the method calls you
expect actually occur during the execution of test. In fact, the one test in `FixmentHandlerTest` makes no
assertions other than the ones implicit in creating the mock objects it uses.

## Future Work

The code written here is very much the "happy path" for the application. For example, `JsonHttpClient` does not throw
any kind of exception if the HTTP request fails or the response body is not valid JSON. My general philosophy is that
one should strongly prefer throwing exceptions rather than returning erroneous results (`null`, `false`, etc.). So
were I developing this codebase for production purposes, that would be the approach I would take.

Of course, one serious problem with this implementation is that each request could possibly require several service
calls. This is quite slow and prone to error. 

A better solution might be to implement a RESTful wrapper API that uses TireSync (which is not very RESTful as-is).
Our wrapper API could use caching to limit the number of live requests we need to make, and could do some
application-specific cleaning of the data.

From there, this little app would be an excellent use-case for a single-page application which could consume our own
internal wrapper API. Getting a big JavaScript framework seemed a bit out of scope for this exercise, but I would
probably recommend something like React or Elm, a strongly-, statically-typed functional language that compiles
to JavaScript (a personal favorite.

