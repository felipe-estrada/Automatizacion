provider "aws" {
  region = "us-west-2" 
}

# Crear VPC
resource "aws_vpc" "main" {
  cidr_block = "10.0.0.0/16"
}

# Crear Internet Gateway
resource "aws_internet_gateway" "main_igw" {
  vpc_id = aws_vpc.main.id
}

# Crear tabla de enrutamiento
resource "aws_route_table" "main_rt" {
  vpc_id = aws_vpc.main.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.main_igw.id
  }
}

# Crear Subnet
resource "aws_subnet" "main" {
  vpc_id     = aws_vpc.main.id
  cidr_block = "10.0.1.0/24"
  map_public_ip_on_launch = true  # IP pública
}

# Asociar la tabla de enrutamiento a la subred
resource "aws_route_table_association" "a" {
  subnet_id      = aws_subnet.main.id
  route_table_id = aws_route_table.main_rt.id
}

# Crear Security Group
resource "aws_security_group" "app_sg" {
  name        = "mySecurityGroup"
  vpc_id      = aws_vpc.main.id
  description = "Allow SSH and HTTP traffic"

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 8080
    to_port     = 8080
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

# Crear la interfaz de red
resource "aws_network_interface" "app_nic" {
  subnet_id   = aws_subnet.main.id
  private_ips = ["10.0.1.10"]
  security_groups = [
    aws_security_group.app_sg.id
  ]
}

# Crear instancia EC2
resource "aws_instance" "app_server" {
  ami           = "ami-0075013580f6322a1"  # Ubuntu 18.04 LTS 
  instance_type = "t2.micro"               # Instancia
  key_name      = "Ansible"                # Clave

  network_interface {
    network_interface_id = aws_network_interface.app_nic.id
    device_index         = 0
  }

  root_block_device {
    volume_size = 30
  }

  tags = {
    Name = "Pagina web"
  }
}
