const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('employees', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    fname: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    middlename: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    lname: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    username: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    slab_type: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    type: {
      type: DataTypes.STRING(20),
      allowNull: false,
      defaultValue: "0"
    },
    dob: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    gender: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    mobile: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "Y"
    },
    department_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    joiningdate: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    email: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    experience: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    martial_status: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    nationality: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    designation_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    file: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    f_h_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    emp_status: {
      type: DataTypes.STRING(95),
      allowNull: true
    },
    p_department: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    p_designation: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    is_drop: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    emp_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    leaving_date: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    alternate_mobile: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    qualification: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    drop_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    image: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    blood_group: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    basic_salary: {
      type: DataTypes.DOUBLE,
      allowNull: true
    },
    description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    token: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'employees',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "id",
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
