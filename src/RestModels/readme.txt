This folder contains classes that model the resources returned from various landlord API calls. This provides a better
developer experience for working with the API resources as the list of properties is known rather than inspecting a
simple array or stdClass object.

RestModel objects are in fact ModelState objects so can support magical Getters and Setters however the raw data is
then extracted for serialization if it requires posting back to the landlord.