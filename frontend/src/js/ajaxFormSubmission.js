/* =========================================
FETCH COURSE ACCORDING DEPARTMENT
========================================= */

const department = document.getElementById("department");
const course = document.getElementById("course");

let controller = null;

if (department && course) {
  department.addEventListener("change", async function () {
    const departmentId = this.value;

    // Reset
    if (!departmentId) {
      course.innerHTML = "<option value=''>Select Course</option>";
      return;
    }

    // Cancel previous request
    if (controller) {
      controller.abort();
    }

    controller = new AbortController();

    try {
      // Loading state
      course.innerHTML = "<option disabled>Loading...</option>";

      const response = await fetch("./ajax/get_courses.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          department_id: departmentId,
        }),
        signal: controller.signal,
      });

      if (!response.ok) {
        throw new Error("Server error");
      }

      const data = await response.text();

      if (!data) {
        throw new Error("Empty response");
      }

      course.innerHTML = data;
    } catch (error) {
      if (error.name === "AbortError") return;

      console.error(error);
      course.innerHTML = "<option>Error loading courses</option>";
    }
  });
}
