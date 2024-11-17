# Minimum CMake version requirement
cmake_minimum_required(VERSION 3.10.2)

# Project name
project(web_serveur)

# Set C++ standard
set(CMAKE_CXX_STANDARD 11)
set(CMAKE_CXX_STANDARD_REQUIRED True)

# Add executable target
add_executable(web_serveur web_serveur.cpp)

# Include RapidJSON headers
# Adjust the include path if your rapidjson headers are located elsewhere
target_include_directories(web_serveur PRIVATE /usr/include/rapidjson)

# Required libraries for networking
target_link_libraries(web_serveur PRIVATE pthread)
