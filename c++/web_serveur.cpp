#include <iostream>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <string>
#include <sstream>
#include <fstream>
#include <cstring>
#include <vector>
#include "rapidjson/document.h"
#include "rapidjson/writer.h"
#include "rapidjson/stringbuffer.h"
#include <cstdlib>  // For system() function
#include <sys/types.h>
#include <signal.h>
#include <sys/wait.h>
#include <thread>
#include <mutex>
#include <unordered_map>

std::unordered_map<int, pid_t> clientExecPids; // Map to store the PIDs of running executables per client
std::mutex execPidsMutex;    // Mutex to protect access to clientExecPids

void sendResponseWithCORS(int socket, const std::string& response) {
    int bytesSent = 0;
    int totalBytesSent = 0;
    while (totalBytesSent < response.size()) {
        bytesSent = send(socket, response.c_str() + totalBytesSent, response.size() - totalBytesSent, 0);
        if (bytesSent < 0) {
            std::cerr << "Could not send response\n";
            break;
        }
        totalBytesSent += bytesSent;
    }
    std::cout << "Sent response to client\n";
}

void sendResponseWithrun(int socket, const std::string& response) {
    std::string headers = "HTTP/1.1 200 OK\r\n";
    headers += "Content-Type: text/plain\r\n";
    headers += "Access-Control-Allow-Origin: *\r\n"; // Allow requests from any origin
    headers += "Access-Control-Allow-Methods: GET, POST\r\n"; // Allow GET and POST methods
    headers += "Access-Control-Allow-Headers: Content-Type\r\n"; // Allow Content-Type header
    headers += "Content-Length: " + std::to_string(response.size()) + "\r\n\r\n";

    std::string fullResponse = headers + response;

    int bytesSent = 0;
    int totalBytesSent = 0;
    while (totalBytesSent < fullResponse.size()) {
        bytesSent = send(socket, fullResponse.c_str() + totalBytesSent, fullResponse.size() - totalBytesSent, 0);
        if (bytesSent < 0) {
            std::cerr << "Could not send response\n";
            break;
        }
        totalBytesSent += bytesSent;
    }

    std::cout << "Sent response to client\n";
}

void sendResponsee(int socket, const std::string& response) {
    std::string headers = "HTTP/1.1 200 OK\r\n";
    headers += "Content-Type: text/html\r\n";
    headers += "Access-Control-Allow-Origin: *\r\n"; // Allow requests from any origin
    headers += "Access-Control-Allow-Methods: GET, POST\r\n"; // Allow GET and POST methods
    headers += "Access-Control-Allow-Headers: Content-Type\r\n"; // Allow Content-Type header
    headers += "Content-Length: " + std::to_string(response.size()) + "\r\n\r\n";

    std::string fullResponse = headers + response;

    int bytesSent = 0;
    int totalBytesSent = 0;
    while (totalBytesSent < fullResponse.size()) {
        bytesSent = send(socket, fullResponse.c_str() + totalBytesSent, fullResponse.size() - totalBytesSent, 0);
        if (bytesSent < 0) {
            std::cerr << "Could not send response\n";
            break;
        }
        totalBytesSent += bytesSent;
    }

    std::cout << "Sent response to client\n";
}

void handleOptionsRequest(int new_wsocket) {
    std::string serverMessage = "HTTP/1.1 200 OK\r\n";
    serverMessage += "Access-Control-Allow-Origin: *\r\n"; // Allow requests from any origin
    serverMessage += "Access-Control-Allow-Methods: GET, POST\r\n"; // Allow GET and POST methods
    serverMessage += "Access-Control-Allow-Headers: Content-Type\r\n"; // Allow Content-Type header
    serverMessage += "Content-Length: 0\r\n\r\n"; // No content for OPTIONS request

    sendResponseWithCORS(new_wsocket, serverMessage);
}

bool readJSONFromFile(const std::string& filename, rapidjson::Document& doc) {
    std::ifstream file(filename);
    if (!file.is_open()) {
        std::cerr << "Failed to open file: " << filename << std::endl;
        return false;
    }

    std::stringstream buffer;
    buffer << file.rdbuf();
    file.close();

    doc.Parse(buffer.str().c_str());
    if (doc.HasParseError()) {
        std::cerr << "Failed to parse JSON file: " << filename << std::endl;
        return false;
    }

    return true;
}

bool writeJSONToFile(const std::string& filename, const rapidjson::Document& doc) {
    rapidjson::StringBuffer buffer;
    rapidjson::Writer<rapidjson::StringBuffer> writer(buffer);
    doc.Accept(writer);

    std::ofstream file(filename);
    if (!file.is_open()) {
        std::cerr << "Failed to open file for writing: " << filename << std::endl;
        return false;
    }

    file << buffer.GetString() << std::endl;
    file.close();

    return true;
}

void sendResponse(int socket, const std::string& response) {
    int bytesSent = 0;
    int totalBytesSent = 0;
    while (totalBytesSent < response.size()) {
        bytesSent = send(socket, response.c_str() + totalBytesSent, response.size() - totalBytesSent, 0);
        if (bytesSent < 0) {
            std::cerr << "Could not send response\n";
            break;
        }
        totalBytesSent += bytesSent;
    }
    std::cout << "Sent response to client\n";
}

void sendErrorResponse(int socket, const std::string& status, const std::string& message) {
    std::string response = "<html><h1>" + status + "</h1><p>" + message + "</html>";

    std::string serverMessage = "HTTP/1.1 " + status + "\r\nContent-Type: text/html\r\nContent-Length: ";
    serverMessage += std::to_string(response.size()) + "\r\n\r\n";
    serverMessage += response;

    sendResponse(socket, serverMessage);
}

void handleFileGet(int new_wsocket) {
    rapidjson::Document doc;
    if (!readJSONFromFile("data.json", doc)) {
        std::cerr << "Failed to read JSON data from file\n";
        sendErrorResponse(new_wsocket, "500 Internal Server Error", "Failed to read JSON data from file");
        return;
    }

    rapidjson::StringBuffer buffer;
    rapidjson::Writer<rapidjson::StringBuffer> writer(buffer);
    doc.Accept(writer);

    std::string jsonData = buffer.GetString();

    std::string serverMessage = "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n";
    serverMessage += "Access-Control-Allow-Origin: *\r\n"; // Allow requests from any origin
    serverMessage += "Access-Control-Allow-Methods: GET, POST\r\n"; // Allow GET and POST methods
    serverMessage += "Access-Control-Allow-Headers: Content-Type\r\n"; // Allow Content-Type header
    serverMessage += "Content-Length: " + std::to_string(jsonData.size()) + "\r\n\r\n";
    serverMessage += jsonData;

    sendResponse(new_wsocket, serverMessage);
}

void handleFilePost(int new_wsocket, const char* buff) {
    std::string requestBody = buff + std::string(buff).find("\r\n\r\n") + 4;

    std::cout << "Request Body: " << requestBody << "\n";

    rapidjson::Document requestDoc;
    requestDoc.Parse(requestBody.c_str());

    if (!requestDoc.IsObject()) {
        std::cerr << "Invalid JSON data received\n";
        sendErrorResponse(new_wsocket, "400 Bad Request", "Invalid JSON data received");
        return;
    }

    rapidjson::Document existingDoc;
    if (!readJSONFromFile("data.json", existingDoc)) {
        std::cerr << "Failed to read existing JSON data\n";
        sendErrorResponse(new_wsocket, "500 Internal Server Error", "Failed to read existing JSON data");
        return;
    }

    if (requestDoc.HasMember("a")) {
        if (requestDoc["a"].IsInt()) {
            existingDoc["a"] = requestDoc["a"].GetInt();
        } else {
            std::cerr << "Invalid value for 'a'\n";
            sendErrorResponse(new_wsocket, "400 Bad Request", "Invalid value for 'a'");
            return;
        }
    }
    if (requestDoc.HasMember("b")) {
        if (requestDoc["b"].IsInt()) {
            existingDoc["b"] = requestDoc["b"].GetInt();
        } else {
            std::cerr << "Invalid value for 'b'\n";
            sendErrorResponse(new_wsocket, "400 Bad Request", "Invalid value for 'b'");
            return;
        }
    }
  if (requestDoc.HasMember("c")) {
        if (requestDoc["c"].IsString()) {
            existingDoc["c"].SetString(requestDoc["c"].GetString(), existingDoc.GetAllocator());
        } else {
            std::cerr << "Invalid value for 'c'\n";
            sendErrorResponse(new_wsocket, "400 Bad Request", "Invalid value for 'c'");
            return;
        }
    }
    if (!writeJSONToFile("data.json", existingDoc)) {
        std::cerr << "Failed to write updated JSON data\n";
        sendErrorResponse(new_wsocket, "500 Internal Server Error", "Failed to write updated JSON data");
        return;
    }

    rapidjson::StringBuffer buffer;
    rapidjson::Writer<rapidjson::StringBuffer> writer(buffer);
    existingDoc.Accept(writer);

    std::string jsonData = buffer.GetString();

    std::string serverMessage = "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n";
    serverMessage += "Access-Control-Allow-Origin: *\r\n"; // Allow requests from any origin
    serverMessage += "Access-Control-Allow-Methods: GET, POST\r\n"; // Allow GET and POST methods
    serverMessage += "Access-Control-Allow-Headers: Content-Type\r\n"; // Allow Content-Type header
    serverMessage += "Content-Length: " + std::to_string(jsonData.size()) + "\r\n\r\n";
    serverMessage += jsonData;

    sendResponse(new_wsocket, serverMessage);
}

void handleRunExecutable(int new_wsocket) {
    std::thread execThread([new_wsocket] {
        pid_t pid = fork();
        if (pid == 0) {
            // Child process
            std::string execPath = "etudiant/GestionEtudiants";
            execl(execPath.c_str(), execPath.c_str(), (char*)NULL);
            _exit(EXIT_FAILURE); // If exec fails
        } else if (pid < 0) {
            // Fork failed
            std::cerr << "Fork failed\n";
        } else {
            // Parent process
            std::lock_guard<std::mutex> lock(execPidsMutex);
            clientExecPids[new_wsocket] = pid; // Store the PID associated with the client socket
            std::cout << "Executable running with PID: " << pid << "\n";
        }
    });

    execThread.detach(); // Detach the thread to run independently

    std::string serverMessage = "HTTP/1.1 200 OK\r\n";
    serverMessage += "Content-Type: text/plain\r\n";
    serverMessage += "Access-Control-Allow-Origin: *\r\n"; // Allow requests from any origin
    serverMessage += "Access-Control-Allow-Methods: GET, POST\r\n"; // Allow GET and POST methods
    serverMessage += "Access-Control-Allow-Headers: Content-Type\r\n"; // Allow Content-Type header
    serverMessage += "Content-Length: " + std::to_string(std::string("Executable is running").size()) + "\r\n\r\n";
    serverMessage += "Executable is running";

    sendResponseWithrun(new_wsocket, serverMessage);
}

void handleStopExecutable(int new_wsocket) {
    std::lock_guard<std::mutex> lock(execPidsMutex);

    auto it = clientExecPids.find(new_wsocket);
    if (it == clientExecPids.end()) {
        std::cerr << "No executable instance running for this client\n";
        sendErrorResponse(new_wsocket, "400 Bad Request", "No executable instance running for this client");
        return;
    }

    pid_t pid = it->second;
    if (kill(pid, SIGKILL) == 0) {
        std::cout << "Killed executable instance with PID: " << pid << "\n";
    } else {
        std::cerr << "Failed to kill executable instance with PID: " << pid << "\n";
    }

    clientExecPids.erase(it); // Remove the PID from the map

    std::string serverMessage = "HTTP/1.1 200 OK\r\n";
    serverMessage += "Content-Type: text/plain\r\n";
    serverMessage += "Access-Control-Allow-Origin: *\r\n"; // Allow requests from any origin
    serverMessage += "Access-Control-Allow-Methods: GET, POST\r\n"; // Allow GET and POST methods
    serverMessage += "Access-Control-Allow-Headers: Content-Type\r\n"; // Allow Content-Type header
    serverMessage += "Content-Length: " + std::to_string(std::string("Executable instance stopped").size()) + "\r\n\r\n";
    serverMessage += "Executable instance stopped";

    sendResponseWithrun(new_wsocket, serverMessage);
}

void processRequest(int new_wsocket) {
    const int bufferSize = 1024;
    char buff[bufferSize];
    memset(buff, 0, bufferSize);
    recv(new_wsocket, buff, bufferSize, 0);
    std::string request(buff);

    std::string requestMethod;
    std::string requestURL;
    std::string requestBody;

    std::istringstream requestStream(request);
    requestStream >> requestMethod >> requestURL;

    if (requestMethod == "GET") {
        if (requestURL == "/file_get") {
            handleFileGet(new_wsocket);
        } else if (requestURL == "/start") {
            handleRunExecutable(new_wsocket);
        } else if (requestURL == "/stop") {
            handleStopExecutable(new_wsocket);
        } else if (requestURL == "/run") {
            handleRunExecutable(new_wsocket);
        } else {
            sendErrorResponse(new_wsocket, "404 Not Found", "The requested URL was not found on this server");
        }
    } else if (requestMethod == "POST") {
        if (requestURL == "/file_post") {
            handleFilePost(new_wsocket, buff);
        } else {
            sendErrorResponse(new_wsocket, "404 Not Found", "The requested URL was not found on this server");
        }
    } else if (requestMethod == "OPTIONS") {
        handleOptionsRequest(new_wsocket);
    } else {
        sendErrorResponse(new_wsocket, "405 Method Not Allowed", "The requested method is not supported");
    }
    close(new_wsocket);
}

int main() {
    const int port = 1107;
    int server_wsocket = socket(AF_INET, SOCK_STREAM, 0);
    if (server_wsocket == 0) {
        std::cerr << "Failed to create socket\n";
        return 1;
    }

    sockaddr_in server_address;
    server_address.sin_family = AF_INET;
    server_address.sin_addr.s_addr = INADDR_ANY;
    server_address.sin_port = htons(port);

    if (bind(server_wsocket, (sockaddr*)&server_address, sizeof(server_address)) < 0) {
        std::cerr << "Failed to bind to port " << port << "\n";
        return 1;
    }

    if (listen(server_wsocket, 3) < 0) {
        std::cerr << "Failed to listen on socket\n";
        return 1;
    }

    std::cout << "Server started on port " << port << "\n";

    while (true) {
        int new_wsocket = accept(server_wsocket, nullptr, nullptr);
        if (new_wsocket < 0) {
            std::cerr << "Failed to accept connection\n";
            continue;
        }

        std::thread requestThread(processRequest, new_wsocket);
        requestThread.detach(); // Detach the thread to handle the request independently
    }

    close(server_wsocket);
    return 0;
}
