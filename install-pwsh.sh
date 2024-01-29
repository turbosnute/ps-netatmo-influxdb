#!/bin/bash

# Check the architecture
if [ $(dpkg --print-architecture) = "amd64" ]; then
    url="https://github.com/PowerShell/PowerShell/releases/download/v7.4.1/powershell-lts_7.4.1-1.deb_amd64.deb"
elif [ $(dpkg --print-architecture) = "arm64" ]; then
    url="https://github.com/PowerShell/PowerShell/releases/download/v7.4.1/powershell-7.4.1-linux-arm64.tar.gz"
elif [ $(dpkg --print-architecture) = "arm32" ]; then
    url="https://github.com/PowerShell/PowerShell/releases/download/v7.4.1/powershell-7.4.1-linux-arm32.tar.gz"
else
    echo "Unsupported architecture: $(`dpkg --print-architecture`)"
    exit 1
fi

# Download the file
wget $url

# Install the file (if applicable)
if echo "$url" | grep -q ".deb"; then
    dpkg -i $(basename $url)
elif echo "$url" | grep -q ".tar.gz"; then
    tar -xvf $(basename $url)  -C /powershell/
    ln -s /powershell/pwsh /bin/pwsh
fi

# Clean Up
rm -f $(basename $url)