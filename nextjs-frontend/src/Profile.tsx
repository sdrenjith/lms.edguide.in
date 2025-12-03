import React from 'react';
import { Mail, Phone } from 'lucide-react';

function Profile() {
  return (
    <div className="min-h-screen bg-[#F2F3F4] flex justify-center items-center">
      <div className="max-w-4xl w-full bg-white shadow-md rounded-lg overflow-hidden">
        <div className="flex">
          {/* Left Section */}
          <div className="w-2/3 p-6">
            <h2 className="text-xl font-bold mb-4">Profile Details</h2>
            <div className="mb-6">
              <h3 className="text-lg font-bold">Personal Information</h3>
              <p>First Name: Oda</p>
              <p>Last Name: Dink</p>
            </div>
            <div className="mb-6">
              <h3 className="text-lg font-bold">Educational Details</h3>
              <p>Degree: Bachelor of Science</p>
              <p>University: Example University</p>
            </div>
            <div className="mb-6">
              <h3 className="text-lg font-bold">Professional Details</h3>
              <p>Position: Programmer</p>
              <p>Company: Example Corp</p>
            </div>
            <div className="mb-6">
              <h3 className="text-lg font-bold">Uploaded Certificates</h3>
              <ul>
                <li>Certificate 1</li>
                <li>Certificate 2</li>
              </ul>
            </div>
            <div className="mb-6">
              <h3 className="text-lg font-bold">Progress Report</h3>
              <div className="mb-2">
                <p>Programming: 78%</p>
                <div className="w-full bg-gray-200 rounded-full h-2.5">
                  <div className="bg-[#FFCE00] h-2.5 rounded-full" style={{ width: '78%' }}></div>
                </div>
              </div>
              <div className="mb-2">
                <p>Prototyping: 65%</p>
                <div className="w-full bg-gray-200 rounded-full h-2.5">
                  <div className="bg-[#FFCE00] h-2.5 rounded-full" style={{ width: '65%' }}></div>
                </div>
              </div>
              <div className="mb-2">
                <p>UI Design: 89%</p>
                <div className="w-full bg-gray-200 rounded-full h-2.5">
                  <div className="bg-[#FFCE00] h-2.5 rounded-full" style={{ width: '89%' }}></div>
                </div>
              </div>
              <div className="mb-2">
                <p>Researching: 94%</p>
                <div className="w-full bg-gray-200 rounded-full h-2.5">
                  <div className="bg-[#FFCE00] h-2.5 rounded-full" style={{ width: '94%' }}></div>
                </div>
              </div>
            </div>
          </div>
          {/* Right Section */}
          <div className="w-1/3 bg-gray-100 p-6">
            <div className="flex flex-col items-center">
              <img src="https://via.placeholder.com/150" alt="Profile" className="rounded-full mb-4 border-4 border-[#FFCE00]" />
              <h3 className="text-lg font-bold">Oda Dink</h3>
              <p className="text-gray-600">Programmer</p>
              <div className="flex mt-4">
                <div className="text-center mr-4">
                  <p className="font-bold">228</p>
                  <p className="text-gray-600">Following</p>
                </div>
                <div className="text-center">
                  <p className="font-bold">4,842</p>
                  <p className="text-gray-600">Followers</p>
                </div>
              </div>
              <div className="mt-4">
                <Phone className="w-5 h-5 inline-block mr-2" />
                <span>+50 123 456 7890</span>
              </div>
              <div className="mt-2">
                <Mail className="w-5 h-5 inline-block mr-2" />
                <span>info@example.com</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Profile; 